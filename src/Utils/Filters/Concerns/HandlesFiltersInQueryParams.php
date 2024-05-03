<?php

namespace Code16\Sharp\Utils\Filters\Concerns;

use Code16\Sharp\Utils\Filters\Filter;
use Illuminate\Support\Collection;

trait HandlesFiltersInQueryParams
{
    public function getFilterValuesFromQueryParams(?array $query): Collection
    {
        return $this->getFilterHandlers()
            ->flatten()
            ->mapWithKeys(function (Filter $handler) use ($query) {
                $value = $handler->fromQueryParam($query['filter_'.$handler->getKey()] ?? null);
                return [
                    $handler->getKey() => $value,
                ];
            })
            ->whereNotNull();
    }
    
    public function getQueryParamsFromFilterValues(array $filterValues): Collection
    {
        return $this->getFilterHandlers()
            ->flatten()
            ->mapWithKeys(function (Filter $handler) use ($filterValues) {
                $value = $handler->toQueryParam($filterValues[$handler->getKey()] ?? null);
                return [
                    'filter_'.$handler->getKey() => $value,
                ];
            });
    }
}
