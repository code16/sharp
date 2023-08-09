<?php

namespace Code16\Sharp\Utils\Filters;

use Illuminate\Contracts\Support\Arrayable;

final class GlobalFilters implements Arrayable
{
    use HandleFilters;

    public function getFilters(): array
    {
        return value(config('sharp.global_filters'));
    }

    public function isEnabled(): bool
    {
        return count($this->getFilters()) > 0;
    }

    public function toArray(): array
    {
        return tap([], function (&$config) {
            $this->appendFiltersToConfig($config);
        });
    }
}
