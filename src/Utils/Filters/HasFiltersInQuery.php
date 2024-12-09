<?php

namespace Code16\Sharp\Utils\Filters;

trait HasFiltersInQuery
{
    public function filterFor(string $filterFullClassNameOrKey): mixed
    {
        $handler = $this->filterContainer->findFilterHandler($filterFullClassNameOrKey);

        if (! $handler || ! isset($this->filterValues[$handler->getKey()])) {
            return null;
        }

        return $this->filterValues[$handler->getKey()];
    }
}
