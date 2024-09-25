<?php

namespace Code16\Sharp\Utils\Context;

use Closure;
use Illuminate\Support\Collection;

class SharpContext
{
    private Collection $cachedInstances;

    public function globalFilterValue()
    {

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