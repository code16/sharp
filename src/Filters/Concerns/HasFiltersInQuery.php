<?php

namespace Code16\Sharp\Filters\Concerns;

use Code16\Sharp\Filters\DateRange\DateRangeFilterValue;

trait HasFiltersInQuery
{
    public function filterFor(string $filterFullClassNameOrKey): string|bool|array|DateRangeFilterValue|null
    {
        $handler = $this->filterContainer->findFilterHandler($filterFullClassNameOrKey);

        if (! $handler || ! isset($this->filterValues[$handler->getKey()])) {
            return null;
        }

        $rawValue = $this->filterValues[$handler->getKey()];

        return $handler->formatRawValue($rawValue);
    }
}
