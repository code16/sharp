<?php

namespace Code16\Sharp\Utils\Filters;

use Closure;
use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Event;

trait HandleFilters
{
    protected ?Collection $filterHandlers = null;

    protected function appendFiltersToConfig(array &$config): void
    {
        $this->getFilterHandlers()
            ->each(function(Filter $filterHandler) use(&$config) {
                $filterConfigData = [
                    "key" => $filterHandler->getKey(),
                    "default" => $this->getFilterDefaultValue($filterHandler),
                    "label" => $filterHandler->getLabel()
                ];

                if($filterHandler instanceof SelectFilter) {
                    $multiple = $filterHandler instanceof SelectMultipleFilter;

                    $filterConfigData += [
                        "type" => 'select',
                        "multiple" => $multiple,
                        "required" => !$multiple && $filterHandler instanceof SelectRequiredFilter,
                        "values" => $this->formatSelectFilterValues($filterHandler),
                        "master" => $filterHandler->isMaster(),
                        "searchable" => $filterHandler->isSearchable(),
                        "searchKeys" => $filterHandler->getSearchKeys(),
                        "template" => $filterHandler->getTemplate()
                    ];
                } elseif($filterHandler instanceof DateRangeFilter) {
                    $filterConfigData += [
                        "type" =>  'daterange',
                        "required" => $filterHandler instanceof DateRangeRequiredFilter,
                        "mondayFirst" => $filterHandler->isMondayFirst(),
                        "displayFormat" => $filterHandler->getDateFormat(),
                    ];
                }

                $config["filters"][] = $filterConfigData;
            });
    }

    private function getFilterHandlers(): Collection
    {
        if($this->filterHandlers === null) {
            $this->filterHandlers = collect($this->getFilters())
                ->map(function($filterHandlerOrClassName) {
                    if(is_string($filterHandlerOrClassName)) {
                        if(!class_exists($filterHandlerOrClassName)) {
                            throw new SharpException("Handler for filter [{$filterHandlerOrClassName}] is invalid");
                        }
                        $filterHandler = app($filterHandlerOrClassName);
                    } else {
                        $filterHandler = $filterHandlerOrClassName;
                    }

                    if(!$filterHandler instanceof Filter) {
                        throw new SharpException("Handler class for filter [{$filterHandlerOrClassName}] must implement a sub-interface of " . Filter::class);
                    }
                    
                    $filterHandler->buildFilterConfig();
                    
                    return $filterHandler;

//        if($callback) {
//            Event::listen("filter-{$filterName}-was-set", function ($value, $params) use($callback) {
//                $callback($value, $params);
//            });
//        }
                });
        }
        
        return $this->filterHandlers;
    }

    protected function formatSelectFilterValues(SelectFilter $handler): array
    {
        $values = $handler->values();
        
        if(!is_array(collect($values)->first())) {
            return collect($values)
                ->map(function ($label, $id) {
                    return compact('id', 'label');
                })
                ->values()
                ->all();
        }
        
        return $values;
    }

    protected function getFilterDefaultValues(): array
    {
        return collect($this->getFilterHandlers())

            // Only filters which aren't in the request
            ->filter(function(Filter $handler) {
                return !request()->has("filter_{$handler->getKey()}");
            })

            // Only required filters or retained filters with value saved in session
            ->filter(function(Filter $handler) {
                return $handler instanceof SelectRequiredFilter
                    || $handler instanceof DateRangeRequiredFilter
                    || $this->isRetainedFilter($handler, true);
            })
            
            ->map(function(Filter $handler) {
                if($this->isRetainedFilter($handler, true)) {
                    return [
                        "name" => $handler->getKey(),
                        "value" => session("_sharp_retained_filter_{$handler->getKey()}")
                    ];
                }

                return [
                    "name" => $handler->getKey(),
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
        collect($this->getFilterHandlers())
            // Only filters sent which are declared "retained"
            ->filter(function(Filter $handler) {
                return request()->has("filter_{$handler->getKey()}")
                    && $this->isRetainedFilter($handler);
            })
            ->each(function($handler) {
                // Array case: we store a coma separated string
                // (to be consistent and only store strings on filter session)
                $value = is_array(request()->get("filter_{$handler->getKey()}"))
                    ? implode(",", request()->get("filter_{$handler->getKey()}"))
                    : request()->get("filter_{$handler->getKey()}");

                if(strlen(trim($value)) === 0) {
                    // No value, we have to unset the retained value
                    session()->forget("_sharp_retained_filter_{$handler->getKey()}");

                } else {
                    session()->put(
                        "_sharp_retained_filter_{$handler->getKey()}",
                        $value
                    );
                }
            });

        session()->save();
    }

    protected function isRetainedFilter(Filter $handler, $onlyValued = false): bool
    {
        return $handler->isRetainInSession()
            && (!$onlyValued || session()->has("_sharp_retained_filter_{$handler->getKey()}"));
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
     */
    protected function getFilterDefaultValue(Filter $handler): int|string|array|null
    {
        if($this->isGlobalFilter($handler)) {
            return session("_sharp_retained_global_filter_{$handler->getKey()}") ?: $handler->defaultValue();
        }

        if($this->isRetainedFilter($handler, true)) {
            $sessionValue = session("_sharp_retained_filter_{$handler->getKey()}");

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