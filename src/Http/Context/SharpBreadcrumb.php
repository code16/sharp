<?php

namespace Code16\Sharp\Http\Context;

use Closure;
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
        $segments = $this->breadcrumbItems();
        $url = $this->baseUrlPrefix();
        $lastIndex = $segments->count() - 1;

        return $segments
            ->map(function (BreadcrumbItem $item, int $index) use (&$url, $lastIndex) {
                $url = $this->appendSegmentToUrl($url, $item);
                $isLeaf = $index === $lastIndex;

                return [
                    'label' => sharp()->config()->get('breadcrumb.display')
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
                '%s/%s',
                $this->baseUrlPrefix(),
                $this->getCurrentPath()
            )
        );
    }

    public function getCurrentPath(): ?string
    {
        return $this->breadcrumbPathFor($this->breadcrumbItems());
    }

    public function getPreviousSegmentUrl(): string
    {
        return url(
            sprintf(
                '%s/%s',
                $this->baseUrlPrefix(),
                $this->breadcrumbPathFor($this->breadcrumbItems()->slice(0, -1))
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

    private function getBreadcrumbLabelFor(BreadcrumbItem $item, bool $isLeaf): string
    {
        return match ($item->type) {
            's-list' => $this->labelForList($item),
            's-dashboard' => $this->labelForDashboard($item),
            's-show' => $this->labelForShow($item, $isLeaf),
            's-form' => $this->labelForForm($item),
            default => $item->key,
        };
    }

    /**
     * Return first part of document title when needed :
     * {documentTitleLabel}, {entityLabel} | {site}.
     */
    private function getDocumentTitleLabelFor(BreadcrumbItem $item, bool $isLeaf): ?string
    {
        if (! $isLeaf) {
            return null;
        }

        $previousItem = $this->previousItemFor($item);

        return match ($item->type) {
            's-show' => $this->labelForShow($item, $isLeaf),
            's-form' => $this->shouldUseEditLabelForForm($item, $previousItem)
                ? trans('sharp::breadcrumb.form.edit_entity', [
                    'entity' => $this->resolveEntityLabelForItem($item, true),
                ])
                : trans('sharp::breadcrumb.form.create_entity', [
                    'entity' => $this->resolveEntityLabelForItem($item, true),
                ]),
            default => null
        };
    }

    private function labelForList(BreadcrumbItem $item): string
    {
        return app(SharpMenuManager::class)
            ->getEntityMenuItem($item->key)
            ?->getLabel() ?: trans('sharp::breadcrumb.entityList');
    }

    private function labelForDashboard(BreadcrumbItem $item): string
    {
        return app(SharpMenuManager::class)
            ->getEntityMenuItem($item->key)
            ?->getLabel() ?: trans('sharp::breadcrumb.dashboard');
    }

    private function labelForShow(BreadcrumbItem $item, bool $isLeaf): string
    {
        return trans('sharp::breadcrumb.show', [
            'entity' => $this->resolveEntityLabelForItem($item, $isLeaf),
        ]);
    }

    private function labelForForm(BreadcrumbItem $item): string
    {
        // A Form is always a leaf
        $previousItem = $this->previousItemFor($item);

        if ($this->shouldUseEntityLabelForForm($item, $previousItem)) {
            // The form entityKey is different from the previous entityKey in the breadcrumb: we are in a EEL case.
            return isset($item->instance)
                ? trans('sharp::breadcrumb.form.edit_entity', [
                    'entity' => $this->resolveEntityLabelForItem($item, true),
                ])
                : trans('sharp::breadcrumb.form.create_entity', [
                    'entity' => $this->resolveEntityLabelForItem($item, true),
                ]);
        }

        return $this->shouldUseEditLabelForForm($item, $previousItem)
            ? trans('sharp::breadcrumb.form.edit')
            : trans('sharp::breadcrumb.form.create');
    }

    private function shouldUseEntityLabelForForm(BreadcrumbItem $item, ?BreadcrumbItem $previousItem): bool
    {
        return ($previousItem->isShow() && ! $this->isSameEntityKeys($previousItem->key, $item->key, true))
            || ($item->isForm() && $this->currentInstanceLabel);
    }

    private function shouldUseEditLabelForForm(BreadcrumbItem $item, ?BreadcrumbItem $previousItem): bool
    {
        return isset($item->instance)
            || ($previousItem->type === 's-show' && ! isset($previousItem->instance));
    }

    public function getParentShowCachedBreadcrumbLabel(): ?string
    {
        $item = $this->breadcrumbItems()->last();

        if (! $item || ! $item->isForm()) {
            return null;
        }

        return $this->resolveParentShowLabel($item);
    }

    /**
     * Only for Shows and Forms.
     */
    private function resolveEntityLabelForItem(BreadcrumbItem $item, bool $isLeaf): string
    {
        if ($isLeaf && $this->currentInstanceLabel) {
            $this->storeCachedLabel($item, $this->currentInstanceLabel);

            return $this->currentInstanceLabel;
        }

        if ($item->isForm()) {
            if ($label = $this->resolveParentShowLabel($item)) {
                return $label;
            }
        }

        if (! $isLeaf) {
            if ($label = $this->getCachedLabel($item)) {
                return $label;
            }
        }

        if ($item->isShow() && ! $isLeaf && ! sharp()->config()->get('breadcrumb.labels.lazy_loading')) {
            if ($label = $this->loadShowBreadcrumbLabel($item)) {
                $this->storeCachedLabel($item, $label);

                return $label;
            }
        }

        return app(SharpEntityManager::class)
            ->entityFor($item->key)
            ->getLabelOrFail((new EntityKey($item->key))->multiformKey());
    }

    private function resolveParentShowLabel(BreadcrumbItem $formItem): ?string
    {
        if (! $formItem->isForm()) {
            return null;
        }

        if ($formItem->instanceId() === null && ! $formItem->isSingleForm()) {
            return null;
        }

        if ($label = $this->getCachedLabel($formItem, 's-show')) {
            return $label;
        }

        if (sharp()->config()->get('breadcrumb.labels.lazy_loading')) {
            return null;
        }

        $items = $this->breadcrumbItems()->values();
        $formIndex = $items->search(fn (BreadcrumbItem $item) => $item->is($formItem));

        if ($formIndex === false) {
            return null;
        }

        $parentShowItem = $items
            ->slice(0, $formIndex)
            ->reverse()
            ->first(fn (BreadcrumbItem $item) => $item->isShow()
                && $item->key === $formItem->key
                && $item->instanceId() === $formItem->instanceId()
            );

        if (! $parentShowItem || ! $this->canLoadShowLabel($parentShowItem)) {
            return null;
        }

        $label = $this->loadShowBreadcrumbLabel($parentShowItem);

        if (! $label) {
            return null;
        }

        $this->storeCachedLabel($parentShowItem, $label);

        return $label;
    }

    private function loadShowBreadcrumbLabel(BreadcrumbItem $showItem): ?string
    {
        if (! $this->canLoadShowLabel($showItem)) {
            return null;
        }

        $show = app(SharpEntityManager::class)
            ->entityFor($showItem->key)
            ->getShowOrFail();

        $show->buildShowConfig();

        if (! $show->getBreadcrumbAttribute()) {
            return null;
        }

        $data = [];

        $this->forceRequestSegments(
            $this->getFakeRequestSegmentsFor($showItem),
            function () use ($show, $showItem, &$data) {
                $data = $show->instance($showItem->instanceId());
            }
        );

        return $show->getBreadcrumbCustomLabel($data);
    }

    private function canLoadShowLabel(BreadcrumbItem $showItem): bool
    {
        if (! $showItem->isShow()) {
            return false;
        }

        if ($showItem->instanceId() !== null) {
            return true;
        }

        return $showItem->isSingleShow();
    }

    private function breadcrumbCacheKey(BreadcrumbItem $item, ?string $type = null): string
    {
        $type = $type ?: $item->type;

        return "sharp.breadcrumb.{$item->key}.{$type}.{$item->instance}";
    }

    private function getCachedLabel(BreadcrumbItem $item, ?string $type = null): ?string
    {
        if (! sharp()->config()->get('breadcrumb.labels.cache')) {
            return null;
        }

        return Cache::get($this->breadcrumbCacheKey($item, $type));
    }

    private function storeCachedLabel(BreadcrumbItem $item, string $label, ?string $type = null): void
    {
        if (! sharp()->config()->get('breadcrumb.labels.cache')) {
            return;
        }

        Cache::put(
            $this->breadcrumbCacheKey($item, $type),
            $label,
            now()->addMinutes((int) sharp()->config()->get('breadcrumb.labels.cache_duration')),
        );
    }

    private function baseUrlPrefix(): string
    {
        return sprintf(
            '%s/%s',
            sharp()->config()->get('custom_url_segment'),
            sharp()->context()->globalFilterUrlSegmentValue(),
        );
    }

    private function appendSegmentToUrl(string $url, BreadcrumbItem $item): string
    {
        return sprintf(
            '%s/%s/%s',
            $url,
            $item->type,
            isset($item->instance) ? "{$item->key}/{$item->instance}" : $item->key,
        );
    }

    private function breadcrumbPathFor(Collection $items): string
    {
        return $items
            ->map(fn (BreadcrumbItem $item) => $item->toUri())
            ->implode('/');
    }

    private function previousItemFor(BreadcrumbItem $item): ?BreadcrumbItem
    {
        return $this->breadcrumbItems()[$item->depth - 1] ?? null;
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
        $this->breadcrumbItems = new Collection();
        $segments = $this->getSegmentsFromRequest();
        $depth = 0;

        if (count($segments) !== 0) {
            if (! in_array($segments[0], ['s-list', 's-show', 's-dashboard'])) {
                return;
            }

            $this->breadcrumbItems->add(
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

                $this->breadcrumbItems->add(
                    (new BreadcrumbItem($type, $key))
                        ->setDepth($depth++)
                        ->setInstance($instance),
                );
            }
        }
    }

    private function getFakeRequestSegmentsFor(BreadcrumbItem $item): Collection
    {
        return $this->breadcrumbItems()
            ->slice(0, $item->depth + 1)
            ->values()
            ->flatMap(fn (BreadcrumbItem $item) => explode('/', $item->toUri()));
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

    public function forceRequestSegments(array|Collection $segments, ?Closure $callback = null): void
    {
        $this->breadcrumbItems = null;
        $this->forcedSegments = collect($segments)->values();

        if ($callback) {
            $callback();

            $this->breadcrumbItems = null;
            $this->forcedSegments = null;
        }
    }
}
