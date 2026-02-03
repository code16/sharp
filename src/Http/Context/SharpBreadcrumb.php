<?php

namespace Code16\Sharp\Http\Context;

use Code16\Sharp\Http\Context\Util\BreadcrumbItem;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class SharpBreadcrumb
{
    public const CURRENT_PAGE_URL_HEADER = 'X-Current-Page-Url';

    private ?string $currentInstanceLabel = null;
    private ?Collection $breadcrumbItems = null;
    private ?Collection $forcedSegments = null;

    public function setCurrentInstanceLabel(?string $label): self
    {
        $this->currentInstanceLabel = $label;

        return $this;
    }

    public function allSegments(): array
    {
        $url = sharp()->config()->get('custom_url_segment')
            .'/'.sharp()->context()->globalFilterUrlSegmentValue();

        return $this
            ->breadcrumbItems()
            ->map(function ($item, $index) use (&$url) {
                $url = sprintf('%s/%s/%s',
                    $url,
                    $item->type,
                    isset($item->instance) ? "{$item->key}/{$item->instance}" : $item->key,
                );
                $isLeaf = $index === count($this->breadcrumbItems()) - 1;

                return [
                    'type' => $this->getFrontTypeNameFor($item->type),
                    'label' => sharp()->config()->get('display_breadcrumb')
                        ? $this->getBreadcrumbLabelFor($item, $isLeaf)
                        : '',
                    'documentTitleLabel' => $this->getDocumentTitleLabelFor($item, $isLeaf),
                    'entityKey' => $item->key,
                    'url' => url($url),
                ];
            })
            ->all();
    }

    public function currentSegment(): ?BreadcrumbItem
    {
        return $this->breadcrumbItems()->last();
    }

    public function previousSegment(): ?BreadcrumbItem
    {
        return $this->breadcrumbItems()->reverse()->skip(1)->first();
    }

    public function previousShowSegment(?string $entityKeyOrClassName = null, ?string $multiformKey = null): ?BreadcrumbItem
    {
        return $this->findPreviousSegment('s-show', $entityKeyOrClassName, $multiformKey);
    }

    public function previousListSegment(?string $entityKeyOrClassName = null): ?BreadcrumbItem
    {
        return $this->findPreviousSegment('s-list', $entityKeyOrClassName);
    }

    public function getCurrentSegmentUrl(): string
    {
        return url(
            sprintf(
                '%s/%s/%s',
                sharp()->config()->get('custom_url_segment'),
                sharp()->context()->globalFilterUrlSegmentValue(),
                $this->getCurrentPath()
            )
        );
    }

    public function getCurrentPath(): ?string
    {
        return $this->breadcrumbItems()
            ->map(fn (BreadcrumbItem $item) => $item->toUri())
            ->implode('/');
    }

    public function getPreviousSegmentUrl(): string
    {
        return url(
            sprintf(
                '%s/%s/%s',
                sharp()->config()->get('custom_url_segment'),
                sharp()->context()->globalFilterUrlSegmentValue(),
                $this->breadcrumbItems()
                    ->slice(0, -1)
                    ->map(fn (BreadcrumbItem $item) => $item->toUri())
                    ->implode('/')
            )
        );
    }

    /**
     * @return Collection<int, BreadcrumbItem>
     */
    public function breadcrumbItems(): Collection
    {
        if (! isset($this->breadcrumbItems)) {
            $this->buildBreadcrumbFromRequest();
        }

        return $this->breadcrumbItems;
    }

    private function findPreviousSegment(string $type, ?string $entityKeyOrClassName = null, ?string $multiformKey = null): ?BreadcrumbItem
    {
        $modeNotEquals = false;
        if ($entityKeyOrClassName && Str::startsWith($entityKeyOrClassName, '!')) {
            $entityKeyOrClassName = Str::substr($entityKeyOrClassName, 1);
            $modeNotEquals = true;
        }

        return $this->breadcrumbItems()
            ->reverse()
            ->filter(fn (BreadcrumbItem $item) => $item->type === $type)
            ->when($entityKeyOrClassName !== null, fn ($items) => $items
                ->filter(fn (BreadcrumbItem $breadcrumbItem) => $modeNotEquals
                    ? ! $breadcrumbItem->entityIs($entityKeyOrClassName, $multiformKey)
                    : $breadcrumbItem->entityIs($entityKeyOrClassName, $multiformKey)
                )
            )
            ->first();
    }

    private function getFrontTypeNameFor(string $type): string
    {
        return match ($type) {
            's-list' => 'list',
            's-form' => 'form',
            's-show' => 'show',
            's-dashboard' => 'dashboard',
            default => '',
        };
    }

    private function getBreadcrumbLabelFor(BreadcrumbItem $item, bool $isLeaf): string
    {
        return $this->resolveLabelForItem($item, $isLeaf, false) ?? $item->key;
    }

    /**
     * Return first part of document title when needed :
     * {documentTitleLabel}, {entityLabel} | {site}.
     */
    private function getDocumentTitleLabelFor(BreadcrumbItem $item, bool $isLeaf): ?string
    {
        return $this->resolveLabelForItem($item, $isLeaf, true);
    }

    public function getParentShowCachedBreadcrumbLabel(): ?string
    {
        $item = $this->breadcrumbItems()->last();

        return Cache::get("sharp.breadcrumb.{$item->key}.s-show.{$item->instance}");
    }

    /**
     * Only for Shows and Forms.
     */
    private function getEntityLabelForInstance(BreadcrumbItem $item, bool $isLeaf): string
    {
        $cacheKey = "sharp.breadcrumb.{$item->key}.{$item->type}.{$item->instance}";

        if ($isLeaf && $this->currentInstanceLabel) {
            Cache::put($cacheKey, $this->currentInstanceLabel, now()->addMinutes(30));

            return $this->currentInstanceLabel;
        }

        if ($item->isForm() && ($cached = $this->getParentShowCachedBreadcrumbLabel())) {
            return $cached;
        }

        if (! $isLeaf) {
            // The breadcrumb custom label may have been cached on the way up
            if ($value = Cache::get($cacheKey)) {
                return $value;
            }
        }

        return app(SharpEntityManager::class)
            ->entityFor($item->key)
            ->getLabelOrFail((new EntityKey($item->key))->multiformKey());
    }

    private function isSameEntityKeys(string $key1, string $key2, bool $compareBaseEntities): bool
    {
        if ($compareBaseEntities) {
            $key1 = (new EntityKey($key1))->baseKey();
            $key2 = (new EntityKey($key2))->baseKey();
        }

        return $key1 === $key2;
    }

    private function buildBreadcrumbFromRequest(): void
    {
        $segments = $this->getSegmentsFromRequest();
        $this->breadcrumbItems = $this->buildItemsFromSegments($segments);
    }

    private function resolveLabelForItem(BreadcrumbItem $item, bool $isLeaf, bool $forDocumentTitle): ?string
    {
        if ($forDocumentTitle && ! $isLeaf) {
            return null;
        }

        return match ($item->type) {
            's-list' => $forDocumentTitle ? null : (app(SharpMenuManager::class)
                ->getEntityMenuItem($item->key)
                ?->getLabel() ?: trans('sharp::breadcrumb.entityList')),
            's-dashboard' => $forDocumentTitle ? null : (app(SharpMenuManager::class)
                ->getEntityMenuItem($item->key)
                ?->getLabel() ?: trans('sharp::breadcrumb.dashboard')),
            's-show' => trans('sharp::breadcrumb.show', [
                'entity' => $this->getEntityLabelForInstance($item, $isLeaf),
            ]),
            's-form' => $this->resolveFormLabel($item, $isLeaf, $forDocumentTitle),
            default => $forDocumentTitle ? null : $item->key,
        };
    }

    private function resolveFormLabel(BreadcrumbItem $item, bool $isLeaf, bool $forDocumentTitle): string
    {
        if ($forDocumentTitle) {
            $previousItem = $this->breadcrumbItems()[$item->depth - 1] ?? null;

            return isset($item->instance) || ($previousItem->type === 's-show' && ! isset($previousItem->instance))
                ? trans('sharp::breadcrumb.form.edit_entity', [
                    'entity' => $this->getEntityLabelForInstance($item, $isLeaf),
                ])
                : trans('sharp::breadcrumb.form.create_entity', [
                    'entity' => $this->getEntityLabelForInstance($item, $isLeaf),
                ]);
        }

        // A Form is always a leaf
        $previousItem = $this->breadcrumbItems()[$item->depth - 1];

        if (
            ($previousItem->isShow() && ! $this->isSameEntityKeys($previousItem->key, $item->key, true))
            || ($item->isForm() && $this->currentInstanceLabel)
        ) {
            // The form entityKey is different from the previous entityKey in the breadcrumb: we are in a EEL case.
            return isset($item->instance)
                ? trans('sharp::breadcrumb.form.edit_entity', [
                    'entity' => $this->getEntityLabelForInstance($item, true),
                ])
                : trans('sharp::breadcrumb.form.create_entity', [
                    'entity' => $this->getEntityLabelForInstance($item, true),
                ]);
        }

        return isset($item->instance) || ($previousItem->type === 's-show' && ! isset($previousItem->instance))
            ? trans('sharp::breadcrumb.form.edit')
            : trans('sharp::breadcrumb.form.create');
    }

    private function buildItemsFromSegments(Collection $segments): Collection
    {
        $items = new Collection();
        $depth = 0;

        if (count($segments) === 0) {
            return $items;
        }

        $items->add(
            (new BreadcrumbItem($segments[0], $segments[1]))->setDepth($depth++),
        );

        $segments = $segments->slice(2)->values();

        while ($segments->count() > 0) {
            $type = $segments->shift();
            $key = $instance = null;
            $segments
                ->takeWhile(fn (string $segment) => ! in_array($segment, ['s-show', 's-form']))
                ->values()
                ->each(function (string $segment, $index) use (&$key, &$instance) {
                    if ($index === 0) {
                        $key = $segment;
                    } elseif ($index === 1) {
                        $instance = $segment;
                    }
                });

            $segments = $segments->slice($instance !== null ? 2 : 1)->values();

            $items->add(
                (new BreadcrumbItem($type, $key))
                    ->setDepth($depth++)
                    ->setInstance($instance),
            );
        }

        return $items;
    }

    protected function getSegmentsFromRequest(): Collection
    {
        if ($this->forcedSegments) {
            return $this->forcedSegments;
        }

        if (request()->wantsJson() || request()->segment(2) === 'api') {
            // API case: we use the X-Current-Page-Url header sent by the front
            // for preloaded EEL we look for 'current_page_url' query
            $urlToParse = request()->header(static::CURRENT_PAGE_URL_HEADER) ?? request()->query('current_page_url');

            return collect(explode('/', parse_url($urlToParse)['path']))
                ->filter(fn (string $segment) => strlen(trim($segment)))
                // Have to skip /sharp/{globalFilter}
                ->skip(2)
                ->values();
        }

        // Have to skip /sharp/{globalFilter}
        return collect(request()->segments())->skip(2)->values();
    }

    public function forceRequestSegments(array|Collection $segments): void
    {
        $this->breadcrumbItems = null;
        $this->forcedSegments = collect($segments)->values();
    }
}
