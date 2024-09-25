<?php

namespace Code16\Sharp\Utils\Context;

use Closure;
use Code16\Sharp\Utils\Filters\GlobalFilters;
use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;
use Illuminate\Support\Collection;

class SharpContext
{
    private Collection $cachedInstances;

    public function globalFilterValue(string $handlerClassOrKey): array|string|null
    {
        $handler = class_exists($handlerClassOrKey)
            ? app($handlerClassOrKey)
            : app(GlobalFilters::class)->findFilter($handlerClassOrKey);

        abort_if(! $handler instanceof GlobalRequiredFilter, 404);

        return $handler->currentValue();
    }

    public function retainedFilterValue()
    {

    }

    final public function findListInstance(string $instanceId, ?Closure $notFoundCallback = null): mixed
    {
        if (isset($this->cachedInstances)) {
            $instance = $this->cachedInstances[$instanceId] ?? null;
        }

        return $instance ?? ($notFoundCallback ? $notFoundCallback($instanceId) : null);
    }

    final public function cacheInstances(?Collection $instances): self
    {
        $this->cachedInstances = $instances ?: collect();

        return $this;
    }
}