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

    public function findFilter(string $key): ?GlobalRequiredFilter
    {
        return collect($this->getFilters())
            ->map(fn ($filter) => instanciate($filter))
            ->each(fn (Filter $filter) => $filter->buildFilterConfig())
            ->filter(fn (Filter $filter) => $filter->getKey() == $key)
            ->first();
    }
}
