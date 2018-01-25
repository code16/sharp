<?php

namespace Code16\Sharp\EntityList\Traits;

use Closure;
use Code16\Sharp\EntityList\EntityListFilter;
use Code16\Sharp\EntityList\EntityListMultipleFilter;
use Code16\Sharp\EntityList\EntityListRequiredFilter;
use Illuminate\Support\Facades\Event;

trait HandleFilters
{
    /**
     * @var array
     */
    protected $filterHandlers = [];

    /**
     * @param string $filterName
     * @param string|EntityListFilter $filterHandler
     * @param Closure|null $callback
     * @return $this
     */
    protected function addFilter(string $filterName, $filterHandler, Closure $callback = null)
    {
        $this->filterHandlers[$filterName] = $filterHandler instanceof EntityListFilter
            ? $filterHandler
            : app($filterHandler);

        if($callback) {
            Event::listen("filter-{$filterName}-was-set", function ($value, $params) use($callback) {
                $callback($value, $params);
            });
        }

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