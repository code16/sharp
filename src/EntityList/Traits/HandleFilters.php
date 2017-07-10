<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\EntityListFilter;

trait HandleFilters
{
    /**
     * @var array
     */
    protected $filterHandlers = [];

    /**
     * @param string $filterName
     * @param string|EntityListFilter $filterHandler
     * @return $this
     */
    protected function addFilter(string $filterName, $filterHandler)
    {
        $this->filterHandlers[$filterName] = $filterHandler instanceof EntityListFilter
            ? $filterHandler
            : app($filterHandler);

        return $this;
    }

    /**
     * @param string $filterName
     * @return string|array
     */
    protected function filterValue(string $filterName)
    {
        return $this->filterHandlers[$filterName]->currentValue();
    }

    /**
     * @param array $config
     */
    protected function appendFiltersToConfig(array &$config)
    {
        foreach($this->filterHandlers as $filterName => $handler) {
            $config["filters"][] = [
                "key" => $filterName,
                "multiple" => $handler->multiple(),
                "values" => $handler->values()
            ];
        }
    }
}