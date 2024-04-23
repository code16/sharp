<?php

namespace Code16\Sharp\Utils\Filters;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasFiltersInQuery
{
    protected Collection $filterHandlers;
    protected array $filterValues;

    public function filterFor(string $filterFullClassNameOrKey): mixed
    {
        $handler = $this->findFilterHandler($filterFullClassNameOrKey);

        if (!$handler || !isset($this->filterValues[$handler->getKey()])) {
            return null;
        }

        return $handler->fromQueryParam($this->filterValues[$handler->getKey()]);
    }

    /**
     * @internal
     */
    public function setDefaultFilters(array $filters): self
    {
        collect($filters)
            ->each(function ($value, $filter) {
                $this->setFilterValue($filter, $value);
            });

        return $this;
    }
    
    /**
     * @internal
     */
    public function getFilterValues(): array
    {
        return $this->filterValues;
    }

    protected function fillFilterWithRequest(array $query = null): void
    {
        $this->filterValues = [];
        
        collect($query)
            ->filter(fn ($value, $name) => Str::startsWith($name, 'filter_'))
            ->mapWithKeys(fn ($value, $name) => [Str::after($name, 'filter_') => $value])
            ->each(fn ($value, $key) => $this->setFilterValue($key, $value));
    }

    protected function setFilterValue(string $filterKey, array|string|null $value): void
    {
        if($filterHandler = $this->findFilterHandler($filterKey)) {
            // Force all filter values to be string, to be consistent with all use cases
            $formattedValue = is_string($value) ? $value : $filterHandler->toQueryParam($value);
            $this->filterValues[$filterHandler->getKey()] = $formattedValue;

            event('filter-'.$filterHandler->getKey().'-was-set', [$formattedValue, $this]);
        }
    }

    private function findFilterHandler(string $filterFullClassNameOrKey): ?Filter
    {
        return $this->filterHandlers
            ->flatten()
            ->filter(function (Filter $filter) use ($filterFullClassNameOrKey) {
                if (class_exists($filterFullClassNameOrKey)) {
                    return $filter instanceof $filterFullClassNameOrKey;
                }
                return $filter->getKey() === $filterFullClassNameOrKey;
            })
            ->first();
    }
}
