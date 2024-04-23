<?php

namespace Code16\Sharp\Utils\Filters;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait HasFiltersInQuery
{
    public function filterFor(string $filterFullClassNameOrKey): mixed
    {
        $handler = $this->filterContainer->findFilterHandler($filterFullClassNameOrKey);

        if (!$handler || !isset($this->filterValues[$handler->getKey()])) {
            return null;
        }

        return $this->filterValues[$handler->getKey()];
    }
    
    protected function fillFilterWithRequest(?array $query): void
    {
        $this->filterValues = [
            ...$this->filterValues,
            ...$this->filterContainer->getFilterValuesFromQueryParams($query),
        ];
        
        foreach ($this->filterValues as $key => $value) {
            $handler = $this->filterContainer->findFilterHandler($key);
            event('filter-'.$handler->getKey().'-was-set', [$value, $this]);
        }
    }
}
