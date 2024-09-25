<?php

namespace Code16\Sharp\Http\Context;

use Code16\Sharp\Http\Context\Util\BreadcrumbItem;
use Code16\Sharp\Utils\Filters\GlobalFilters;
use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CurrentSharpRequest
{
    protected ?Collection $breadcrumb = null;

    public const CURRENT_PAGE_URL_HEADER = 'X-Current-Page-Url';

    public function breadcrumb(): Collection
    {
        if ($this->breadcrumb === null) {
            $this->buildBreadcrumb();
        }

        return $this->breadcrumb;
    }

    public function getCurrentBreadcrumbItem(): ?BreadcrumbItem
    {
        return $this->breadcrumb()->last();
    }

    public function getPreviousShowFromBreadcrumbItems(?string $entityKey = null): ?BreadcrumbItem
    {
        $modeNotEquals = false;
        if ($entityKey && Str::startsWith($entityKey, '!')) {
            $entityKey = Str::substr($entityKey, 1);
            $modeNotEquals = true;
        }

        return $this->breadcrumb()
            ->reverse()
            ->filter->isShow()
            ->when($entityKey !== null, function ($items) use ($entityKey, $modeNotEquals) {
                return $items
                    ->filter(function (BreadcrumbItem $breadcrumbItem) use ($entityKey, $modeNotEquals) {
                        return $modeNotEquals
                            ? $breadcrumbItem->entityKey() !== $entityKey
                            : $breadcrumbItem->entityKey() === $entityKey;
                    });
            })
            ->first();
    }

    public function getUrlForBreadcrumbItem(BreadcrumbItem $item): string
    {
        $breadcrumb = $this->breadcrumb();
        while ($breadcrumb->count() && ! $breadcrumb->last()->is($item)) {
            $breadcrumb = $breadcrumb->slice(0, -1);
        }

        return $this->getUrlForBreadcrumb($breadcrumb);
    }

    public function getUrlForBreadcrumb(Collection $breadcrumb): string
    {
        return url(
            sprintf(
                '%s/%s',
                sharp()->config()->get('custom_url_segment'),
                $breadcrumb
                    ->map(fn (BreadcrumbItem $item) => $item->toUri())
                    ->implode('/'),
            )
        );
    }

    public function getUrlOfPreviousBreadcrumbItem(string $type = null): string
    {
        $breadcrumb = $this->breadcrumb()->slice(0, -1);
        if ($type) {
            while ($breadcrumb->count() && $type !== $breadcrumb->last()->type) {
                $breadcrumb = $breadcrumb->slice(0, -1);
            }
        }

        return $this->getUrlForBreadcrumb($breadcrumb);
    }

    public function getCurrentEntityMenuLabel(): ?string
    {
        if ($currentEntityKey = $this->entityKey()) {
            return $this->getEntityMenuLabel($currentEntityKey);
        }

        return null;
    }

    public function getEntityMenuLabel(string $entityKey): ?string
    {
        return app(SharpMenuManager::class)
            ->getEntityMenuItem($entityKey)
            ?->getLabel();
    }

    public function isEntityList(): bool
    {
        $current = $this->getCurrentBreadcrumbItem();

        return $current && $current->isEntityList();
    }

    public function isShow(): bool
    {
        $current = $this->getCurrentBreadcrumbItem();

        return $current && $current->isShow();
    }

    public function isForm(): bool
    {
        $current = $this->getCurrentBreadcrumbItem();

        return $current && $current->isForm();
    }

    public function isCreation(): bool
    {
        $current = $this->getCurrentBreadcrumbItem();

        return $current
            && $current->isForm()
            && ! $current->isSingleForm()
            && $current->instanceId() === null;
    }

    public function isUpdate(): bool
    {
        $current = $this->getCurrentBreadcrumbItem();

        return $current
            && $current->isForm()
            && ($current->instanceId() !== null || $current->isSingleForm());
    }

    public function entityKey(): ?string
    {
        $current = $this->getCurrentBreadcrumbItem();

        return $current?->entityKey();
    }

    public function instanceId(): ?string
    {
        $current = $this->getCurrentBreadcrumbItem();

        return $current?->instanceId();
    }

    final public function globalFilterFor(string $handlerClassOrKey): array|string|null
    {
        $handler = class_exists($handlerClassOrKey)
            ? app($handlerClassOrKey)
            : app(GlobalFilters::class)->findFilter($handlerClassOrKey);

        abort_if(! $handler instanceof GlobalRequiredFilter, 404);

        return $handler->currentValue();
    }

    private function buildBreadcrumb(): void
    {
        $this->breadcrumb = new Collection();
        $segments = $this->getSegmentsFromRequest();
        $depth = 0;

        if (count($segments) !== 0) {
            $this->breadcrumb->add(
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

                $this->breadcrumb->add(
                    (new BreadcrumbItem($type, $key))
                        ->setDepth($depth++)
                        ->setInstance($instance),
                );
            }
        }
    }

    protected function getSegmentsFromRequest(): Collection
    {
        if (request()->wantsJson() || request()->segment(2) === 'api') {
            // API case: we use the X-Current-Page-Url header sent by the front
            // for preloaded EEL we look for 'current_page_url' query
            $urlToParse = request()->header(static::CURRENT_PAGE_URL_HEADER) ?? request()->query('current_page_url');

            return collect(explode('/', parse_url($urlToParse)['path']))
                ->filter(fn (string $segment) => strlen(trim($segment))
                    && $segment !== sharp()->config()->get('custom_url_segment')
                )
                ->values();
        }

        return collect(request()->segments())->slice(1)->values();
    }
}
