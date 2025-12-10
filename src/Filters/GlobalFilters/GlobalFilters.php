<?php

namespace Code16\Sharp\Filters\GlobalFilters;

use Code16\Sharp\Filters\Concerns\HasFilters;
use Code16\Sharp\Filters\Filter;
use Code16\Sharp\Filters\GlobalRequiredFilter;
use Illuminate\Contracts\Support\Arrayable;

final class GlobalFilters implements Arrayable
{
    use HasFilters;

    public static string $defaultKey = 'root';

    public function getFilters(): array
    {
        return collect(sharp()->config()->get('global_filters'))
            ->filter(fn (GlobalRequiredFilter $filter) => $filter->authorize())
            ->all();
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
                'valuated' => [], // not needed here
            ],
        ];
    }

    public function findFilter(string $key): ?Filter
    {
        return $this->filterContainer()->findFilterHandler($key);
    }
}
