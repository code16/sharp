<?php

namespace Code16\Sharp\Utils\Filters;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Event;

trait HandleFilters
{
    /**
     * @var array
     */
    protected $filterHandlers = [];

    /**
     * @param string $filterName
     * @param string|ListFilter $filterHandler
     * @param Closure|null $callback
     * @return $this
     */
    protected function addFilter(string $filterName, $filterHandler, Closure $callback = null)
    {
        $this->filterHandlers[$filterName] = $filterHandler instanceof ListFilter
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
            $multiple = $handler instanceof ListMultipleFilter;
            $required = !$multiple && $handler instanceof ListRequiredFilter;

            $config["filters"][] = [
                "key" => $filterName,
                "multiple" => $multiple,
                "required" => $required,
                "default" => $required ? $handler->defaultValue() : null,
                "values" => $this->formatFilterValues($handler),
                "label" => method_exists($handler, "label") ? $handler->label() : $filterName,
                "master" => method_exists($handler, "isMaster") ? $handler->isMaster() : false,
                "searchable" => method_exists($handler, "isSearchable") ? $handler->isSearchable() : false,
                "searchKeys" => method_exists($handler, "searchKeys") ? $handler->searchKeys() : ["label"],
                "template" => $this->formatFilterTemplate($handler)
            ];
        }
    }

    /**
     * @param ListFilter $handler
     * @return array
     */
    protected function formatFilterValues(ListFilter $handler)
    {
        if(!method_exists($handler, "template")) {
            return collect($handler->values())->map(function ($label, $id) {
                return compact('id', 'label');
            })->values()->all();
        }

        // There is a user-defined template: just return the raw values() is this case
        return $handler->values();
    }

    /**
     * @param ListFilter $handler
     * @return string
     */
    protected function formatFilterTemplate(ListFilter $handler)
    {
        if(!method_exists($handler, "template")) {
            return '{{label}}';
        }

        if(($template = $handler->template()) instanceof View) {
            return $template->render();
        }

        return $template;
    }
}