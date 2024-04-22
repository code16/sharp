<?php

namespace Code16\Sharp\Utils\Filters;

use Illuminate\Contracts\Support\Arrayable;

final class GlobalFilters implements Arrayable
{
    use HandleFilters;

    public function getFilters(): array
    {
        return sharpConfig()->get('global_filters');
    }

    public function isEnabled(): bool
    {
        return count($this->getFilters()) > 0;
    }

    public function toArray(): array
    {
        $config = [];
        $this->appendFiltersToConfig($config);
        
        return [
            'config' => $config,
            'filterValues' => $this->getFilterValuesToFront(),
        ];
    }
    
    public function getFilterValuesToFront(): array
    {
        return [
            'default' => $this->getFilterHandlers()
                ->flatten()
                ->mapWithKeys(function (Filter $handler) {
                    return [$handler->getKey() => $handler->defaultValue()];
                })
                ->toArray(),
            'current' => $this->getFilterHandlers()
                ->flatten()
                ->mapWithKeys(function (Filter $handler) {
                    return [$handler->getKey() => $handler->currentValue()];
                })
                ->toArray(),
        ];
    }

    public function findFilter(string $key): ?GlobalRequiredFilter
    {
        return collect($this->getFilters())
            ->each(fn (Filter $filter) => $filter->buildFilterConfig())
            ->filter(fn (Filter $filter) => $filter->getKey() == $key)
            ->first();
    }
}
