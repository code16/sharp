<?php

namespace Code16\Sharp\EntityList\Traits;

use Code16\Sharp\EntityList\EntityListFilter;
use Code16\Sharp\EntityList\EntityListMultipleFilter;
use Code16\Sharp\EntityList\EntityListRequiredFilter;

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
     * @param array $config
     */
    protected function appendFiltersToConfig(array &$config)
    {
        foreach($this->filterHandlers as $filterName => $handler) {
            $multiple = $handler instanceof EntityListMultipleFilter;
            $required = !$multiple && $handler instanceof EntityListRequiredFilter;

            $config["filters"][] = [
                "key" => $filterName,
                "multiple" => $multiple,
                "required" => $required,
                "default" => $required ? $handler->defaultValue() : null,
                "values" => $this->formatFilterValues($handler->values()),
                "label" => method_exists($handler, "label") ? $handler->label() : $filterName
            ];
        }
    }

    /**
     * @param array $values
     * @return array
     */
    protected function formatFilterValues(array $values)
    {
        return collect($values)->map(function($label, $id) {
            return compact('id', 'label');
        })->values()->all();
    }
}