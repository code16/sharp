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
        return [
            'config' => [
                'filters' => $this->filterContainer()->getFiltersConfigArray(),
            ],
            'filterValues' => [
                'default' => $this->filterContainer()->getFilterHandlers()
                    ->flatten()
                    ->mapWithKeys(function (Filter $handler) {
                        return [$handler->getKey() => $handler->defaultValue()];
                    })
                    ->toArray(),
                'current' => $this->filterContainer()->getFilterHandlers()
                    ->flatten()
                    ->mapWithKeys(function (Filter $handler) {
                        return [$handler->getKey() => $handler->currentValue()];
                    })
                    ->toArray(),
            ],
        ];
    }
    
    public function findFilter(string $key): ?Filter
    {
        return $this->filterContainer()->findFilterHandler($key);
    }
}
