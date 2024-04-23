<?php

namespace Code16\Sharp\Utils\Filters;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasFiltersInQuery
{
    protected Collection $filterHandlers;
    protected array $filterValues;

    public function filterFor(string $filterFullClassNameOrKey): mixed
    {
        $handler = $this->filterHandlers
            ->flatten()
            ->filter(function (Filter $filter) use ($filterFullClassNameOrKey) {
                if (class_exists($filterFullClassNameOrKey)) {
                    return $filter instanceof $filterFullClassNameOrKey;
                }
                return $filter->getKey() === $filterFullClassNameOrKey;
            })
            ->first();

        if (!$handler || !isset($this->filterValues[$handler->getKey()])) {
            return null;
        }

        return $handler->fromQueryParam($this->filterValues[$handler->getKey()]);
    }

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
    public function getFilterValues(): array {
        return $this->filterValues;
    }

    protected function fillFilterWithRequest(array $query = null): void
    {
        $this->filterValues = [];
        
        collect($query)
            ->filter(fn ($value, $name) => Str::startsWith($name, 'filter_'))
            ->each(function ($value, $name) {
                $this->setFilterValue(Str::after($name, 'filter_'), $value);
            });
    }

    protected function setFilterValue(string $filter, array|string|null $value): void
    {
        if (is_array($value)) {
            // Force all filter values to be string, to be consistent with all use cases
            // (filter in EntityList or in Command)
            if (empty($value)) {
                $value = null;
            } elseif (($value['start'] ?? null) instanceof Carbon) {
                // RangeFilter case
                $value = collect($value)
                    ->map->format('Ymd')
                    ->implode('..');
            } else {
                // Multiple filter case
                $value = implode(',', $value);
            }
        }

        $this->filterValues[$filter] = $value;

        event("filter-{$filter}-was-set", [$value, $this]);
    }
}
