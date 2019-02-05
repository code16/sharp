<?php

namespace Code16\Sharp\Utils\Filters;

use Closure;
use Code16\Sharp\EntityList\EntityListRequiredFilter;
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

    /**
     * @return array
     */
    protected function getFilterDefaultValues()
    {
        return collect($this->filterHandlers)

            // Only filters which aren't in the request
            ->filter(function($handler, $attribute) {
                return !request()->has("filter_$attribute");
            })

            // Only required filters or retained filters with value saved in session
            ->filter(function($handler, $attribute) {
                return $handler instanceof EntityListRequiredFilter
                    || $this->isRetainedFilter($handler, $attribute, true);
            })

            ->map(function($handler, $attribute) {
                if($this->isRetainedFilter($handler, $attribute, true)) {
                    return [
                        "name" => $attribute,
                        "value" => session("_sharp_filter_$attribute")
                    ];
                }

                return [
                    "name" => $attribute,
                    "value" => $handler->defaultValue()
                ];
            })
            ->pluck("value", "name")
            ->all();
    }

    /**
     * Save "retain" filter values in session. Retain filters
     * are those whose handler is defining a retainValueInSession()
     * function which returns true.
     */
    protected function putRetainedFilterValuesInSession()
    {
        collect($this->filterHandlers)
            ->filter(function($handler, $attribute) {
                return request()->has("filter_$attribute")
                    && $this->isRetainedFilter($handler, $attribute);
            })
            ->each(function($handler, $attribute) {
                session()->put(
                    "_sharp_filter_$attribute",
                    request()->get("filter_$attribute")
                );
            });

        session()->save();
    }

    /**
     * @param $handler
     * @param $attribute
     * @param bool $onlyValued
     * @return bool
     */
    protected function isRetainedFilter($handler, $attribute, $onlyValued = false)
    {
        return method_exists($handler, "retainValueInSession")
            && $handler->retainValueInSession()
            && (!$onlyValued || session()->has("_sharp_filter_$attribute"));
    }
}