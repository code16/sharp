<?php

namespace Code16\Sharp\Filters\GlobalFilters;

use Code16\Sharp\Filters\Concerns\HasFilters;
use Code16\Sharp\Filters\Filter;
use Code16\Sharp\Filters\GlobalRequiredFilter;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Collection;

final class GlobalFilters implements Arrayable
{
    use HasFilters;

    public static string $defaultKey = 'root';
    public static string $valuesUrlSeparator = '~';
    private ?Collection $globalFilters = null;

    public function getFilters(): array
    {
        if ($this->globalFilters === null) {
            $this->globalFilters = collect(sharp()->config()->get('global_filters'))
                ->filter(fn (GlobalRequiredFilter $filter) => $filter->authorize() && count($filter->cachedValues()))
                ->values();
        }

        return $this->globalFilters->all();
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
                    ->mapWithKeys(fn (Filter $handler) => [$handler->getKey() => $handler->defaultValue()])
                    ->toArray(),
                'current' => $this->filterContainer()->getFilterHandlers()
                    ->flatten()
                    ->mapWithKeys(fn (Filter $handler) => [$handler->getKey() => $handler->currentValue()])
                    ->toArray(),
                'valuated' => [], // not needed here
            ],
        ];
    }

    public function isDeclared(string $key): bool
    {
        return $this->getDeclaredFilter($key) !== null;
    }

    public function getDeclaredFilter(string $key): ?GlobalRequiredFilter
    {
        return collect(sharp()->config()->get('global_filters'))
            ->first(function (GlobalRequiredFilter $filter) use ($key) {
                $filter->buildFilterConfig();

                if (class_exists($key)) {
                    return $filter instanceof $key;
                }

                return $filter->getKey() === $key;
            });
    }

    public function findFilter(string $key): ?Filter
    {
        return $this->filterContainer()->findFilterHandler($key);
    }
}
