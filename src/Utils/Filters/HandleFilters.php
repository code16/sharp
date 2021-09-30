<?php

namespace Code16\Sharp\Utils\Filters;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Event;

trait HandleFilters
{
    protected array $filterHandlers = [];

    /**
     * @param string $filterName
     * @param string|Filter $filterHandler
     * @param Closure|null $callback
     * @return $this
     */
    protected function addFilter(string $filterName, $filterHandler, Closure $callback = null): self
    {
        $this->filterHandlers[$filterName] = $filterHandler instanceof Filter
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
    protected function appendFiltersToConfig(array &$config): void
    {
        foreach($this->filterHandlers as $filterName => $handler) {
            $filterConfigData = [
                "key" => $filterName,
                "default" => $this->getFilterDefaultValue($handler, $filterName),
                "label" => method_exists($handler, "label") ? $handler->label() : $filterName,
            ];

            if($handler instanceof SelectFilter) {
                $multiple = $handler instanceof SelectMultipleFilter;

                $filterConfigData += [
                    "type" => 'select',
                    "multiple" => $multiple,
                    "required" => !$multiple && $handler instanceof SelectRequiredFilter,
                    "values" => $this->formatSelectFilterValues($handler),
                    "master" => method_exists($handler, "isMaster") ? $handler->isMaster() : false,
                    "searchable" => method_exists($handler, "isSearchable") ? $handler->isSearchable() : false,
                    "searchKeys" => method_exists($handler, "searchKeys") ? $handler->searchKeys() : ["label"],
                    "template" => $this->formatSelectFilterTemplate($handler)
                ];

            } elseif($handler instanceof DateRangeFilter) {
                $filterConfigData += [
                    "type" =>  'daterange',
                    "required" => $handler instanceof DateRangeRequiredFilter,
                    "mondayFirst" => method_exists($handler, "isMondayFirst") ? $handler->isMondayFirst() : true,
                    "displayFormat" => method_exists($handler, "dateFormat") ? $handler->dateFormat() : "MM-DD-YYYY"
                ];
            }

            $config["filters"][] = $filterConfigData;
        }
    }

    protected function formatSelectFilterValues(SelectFilter $handler): array
    {
        if(!method_exists($handler, "template")) {
            return collect($handler->values())
                ->map(function ($label, $id) {
                    return compact('id', 'label');
                })
                ->values()
                ->all();
        }

        // There is a user-defined template: just return the raw values() is this case
        return $handler->values();
    }

    protected function formatSelectFilterTemplate(SelectFilter $handler): string
    {
        if(!method_exists($handler, "template")) {
            return '{{label}}';
        }

        if(($template = $handler->template()) instanceof View) {
            return $template->render();
        }

        return $template;
    }

    protected function getFilterDefaultValues(): array
    {
        return collect($this->filterHandlers)

            // Only filters which aren't in the request
            ->filter(function($handler, $attribute) {
                return !request()->has("filter_$attribute");
            })

            // Only required filters or retained filters with value saved in session
            ->filter(function($handler, $attribute) {
                return $handler instanceof SelectRequiredFilter
                    || $handler instanceof DateRangeRequiredFilter
                    || $this->isRetainedFilter($handler, $attribute, true);
            })
            
            ->map(function($handler, $attribute) {
                if($this->isRetainedFilter($handler, $attribute, true)) {
                    return [
                        "name" => $attribute,
                        "value" => session("_sharp_retained_filter_$attribute")
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
    protected function putRetainedFilterValuesInSession(): void
    {
        collect($this->filterHandlers)
            // Only filters sent which are declared "retained"
            ->filter(function($handler, $attribute) {
                return request()->has("filter_$attribute")
                    && $this->isRetainedFilter($handler, $attribute);
            })
            ->each(function($handler, $attribute) {
                // Array case: we store a coma separated string
                // (to be consistent and only store strings on filter session)
                $value = is_array(request()->get("filter_$attribute"))
                    ? implode(",", request()->get("filter_$attribute"))
                    : request()->get("filter_$attribute");

                if(strlen(trim($value)) === 0) {
                    // No value, we have to unset the retained value
                    session()->forget("_sharp_retained_filter_$attribute");

                } else {
                    session()->put(
                        "_sharp_retained_filter_$attribute",
                        $value
                    );
                }
            });

        session()->save();
    }

    protected function isRetainedFilter(Filter $handler, string $attribute, $onlyValued = false): bool
    {
        return method_exists($handler, "retainValueInSession")
            && $handler->retainValueInSession()
            && (!$onlyValued || session()->has("_sharp_retained_filter_$attribute"));
    }

    protected function isGlobalFilter(Filter $handler): bool
    {
        return $handler instanceof GlobalRequiredFilter;
    }

    /**
     * Return the filter default value, which can be, in that order:
     * - the retained value, if the filter is retained
     * - the default value is the filter is required
     * - or null
     *
     * @param $handler
     * @param string $attribute
     * @return int|string|array|null
     */
    protected function getFilterDefaultValue(Filter $handler, string $attribute)
    {
        if($this->isGlobalFilter($handler)) {
            return session("_sharp_retained_global_filter_$attribute") ?: $handler->defaultValue();
        }

        if($this->isRetainedFilter($handler, $attribute, true)) {
            $sessionValue = session("_sharp_retained_filter_$attribute");

            if($handler instanceof SelectMultipleFilter) {
                return explode(",", $sessionValue);
            }

            if($handler instanceof DateRangeFilter) {
                list($start, $end) = collect(explode("..", $sessionValue))
                    ->map(function($date) {
                        if(strlen($date) == 8) {
                            // Date was stored with a Ymd format, we need Y-m-d
                            return sprintf("%s-%s-%s",
                                substr($date, 0, 4),
                                substr($date, 4, 2),
                                substr($date, 6, 2)
                            );
                        }

                        return $date;
                    })
                    ->toArray();

                return compact("start", "end");
            }

            return $sessionValue;
        }

        if($handler instanceof SelectRequiredFilter) {
            return $handler->defaultValue();
        }

        if($handler instanceof DateRangeRequiredFilter) {
            return collect($handler->defaultValue())
                ->map->format("Y-m-d")->toArray();
        }

        return null;
    }
}