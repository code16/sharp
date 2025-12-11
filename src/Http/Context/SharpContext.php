<?php

namespace Code16\Sharp\Http\Context;

use Closure;
use Code16\Sharp\Filters\GlobalFilters\GlobalFilters;
use Code16\Sharp\Filters\GlobalRequiredFilter;
use Illuminate\Support\Collection;

class SharpContext
{
    private Collection $cachedListInstances;
    private SharpBreadcrumb $breadcrumb;

    public function globalFilterValue(string $handlerClassOrKey): array|string|null
    {
        $handler = class_exists($handlerClassOrKey)
            ? app($handlerClassOrKey)
            : app(GlobalFilters::class)->findFilter($handlerClassOrKey);

        abort_if(! $handler instanceof GlobalRequiredFilter, 404);

        return $handler->currentValue();
    }

    public function globalFilterUrlSegmentValue(): string
    {
        return collect(sharp()->config()->get('global_filters'))
            ->map(fn ($globalFilterClassOrInstance) => is_string($globalFilterClassOrInstance)
                ? app($globalFilterClassOrInstance)
                : $globalFilterClassOrInstance
            )
            ->map(fn (GlobalRequiredFilter $globalFilter) => $globalFilter->currentValue())
            ->filter()
            ->implode(':') ?: GlobalFilters::$defaultKey;
    }

    public function retainedFilterValue(string $handlerClassOrKey): array|string|null
    {
        $key = class_exists($handlerClassOrKey)
            ? app($handlerClassOrKey)->getKey()
            : $handlerClassOrKey;

        return session()->get('_sharp_retained_filter_'.$key);
    }

    public function breadcrumb(): SharpBreadcrumb
    {
        if (! isset($this->breadcrumb)) {
            $this->breadcrumb = app(SharpBreadcrumb::class);
        }

        return $this->breadcrumb;
    }

    public function isEntityList(): bool
    {
        $current = $this->breadcrumb()->currentSegment();

        return $current && $current->isEntityList();
    }

    public function isShow(): bool
    {
        $current = $this->breadcrumb()->currentSegment();

        return $current && $current->isShow();
    }

    public function isForm(): bool
    {
        $current = $this->breadcrumb()->currentSegment();

        return $current && $current->isForm();
    }

    public function isCreation(): bool
    {
        $current = $this->breadcrumb()->currentSegment();

        return $current
            && $current->isForm()
            && ! $current->isSingleForm()
            && $current->instanceId() === null;
    }

    public function isUpdate(): bool
    {
        $current = $this->breadcrumb()->currentSegment();

        return $current
            && $current->isForm()
            && ($current->instanceId() !== null || $current->isSingleForm());
    }

    public function entityKey(): ?string
    {
        $current = $this->breadcrumb()->currentSegment();

        return $current?->entityKey();
    }

    public function instanceId(): ?string
    {
        $current = $this->breadcrumb()->currentSegment();

        return $current?->instanceId();
    }

    public function isFormRefresh(): bool
    {
        return request()->routeIs('code16.sharp.api.form.refresh.update');
    }

    public function findListInstance(string $instanceId, ?Closure $notFoundCallback = null): mixed
    {
        if (isset($this->cachedListInstances)) {
            $instance = $this->cachedListInstances[$instanceId] ?? null;
        }

        return $instance ?? ($notFoundCallback ? $notFoundCallback($instanceId) : null);
    }

    public function cacheListInstances(?Collection $instances): self
    {
        $this->cachedListInstances = $instances ?: collect();

        return $this;
    }
}
