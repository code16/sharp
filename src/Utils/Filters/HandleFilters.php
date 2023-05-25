<?php

namespace Code16\Sharp\Utils\Filters;

use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Support\Collection;

trait HandleFilters
{
    protected ?Collection $filterHandlers = null;

    protected function appendFiltersToConfig(array &$config): void
    {
        $this->getFilterHandlers()
            ->each(function (Collection $filterHandlers, string $positionKey) use (&$config) {
                if($filterHandlers->count() === 0) {
                    return;
                }
                
                $config['filters'][$positionKey] = $filterHandlers
                    ->map(function (Filter $filterHandler) {
                        $filterConfigData = [
                            'key' => $filterHandler->getKey(),
                            'default' => $this->getFilterDefaultValue($filterHandler),
                            'label' => $filterHandler->getLabel(),
                        ];

                        if ($filterHandler instanceof SelectFilter) {
                            $multiple = $filterHandler instanceof SelectMultipleFilter;

                            $filterConfigData += [
                                'type' => 'select',
                                'multiple' => $multiple,
                                'required' => !$multiple && $filterHandler instanceof SelectRequiredFilter,
                                'values' => $this->formatSelectFilterValues($filterHandler),
                                'master' => $filterHandler->isMaster(),
                                'searchable' => $filterHandler->isSearchable(),
                                'searchKeys' => $filterHandler->getSearchKeys(),
                                'template' => $filterHandler->getTemplate(),
                            ];
                        } elseif ($filterHandler instanceof DateRangeFilter) {
                            $filterConfigData += [
                                'type' => 'daterange',
                                'required' => $filterHandler instanceof DateRangeRequiredFilter,
                                'mondayFirst' => $filterHandler->isMondayFirst(),
                                'displayFormat' => $filterHandler->getDateFormat(),
                            ];
                        } elseif ($filterHandler instanceof CheckFilter) {
                            $filterConfigData += [
                                'type' => 'check',
                            ];
                        }

                        return $filterConfigData;
                    })
                    ->toArray();
            });
    }

    private function getFilterHandlers(): Collection
    {
        if ($this->filterHandlers === null) {
            $this->filterHandlers = collect($this->getFilters())

                // First get filters which are section-based...
                ->filter(fn ($value, $index) => is_array($value))

                // ... and merge filters set for the whole page
                ->merge(
                    [
                        '_page' => collect($this->getFilters())
                            ->filter(fn ($value, $index) => ! is_array($value)),
                    ]
                )

                ->map(function ($handlers) {
                    return collect($handlers)
                        ->map(function ($filterHandlerOrClassName)  {
                            if (is_string($filterHandlerOrClassName)) {
                                if (!class_exists($filterHandlerOrClassName)) {
                                    throw new SharpException(sprintf(
                                        'Handler for filter [%s] is invalid',
                                        $filterHandlerOrClassName
                                    ));
                                }
                                $filterHandler = app($filterHandlerOrClassName);
                            } else {
                                $filterHandler = $filterHandlerOrClassName;
                            }

                            if (!$filterHandler instanceof Filter) {
                                throw new SharpException(sprintf(
                                    'Handler class for filter [%s] must implement a sub-interface of [%s]',
                                    $filterHandlerOrClassName,
                                    Filter::class
                                ));
                            }

                            $filterHandler->buildFilterConfig();

                            return $filterHandler;
                        });
                });
        }

        return $this->filterHandlers;
    }

    protected function formatSelectFilterValues(SelectFilter $handler): array
    {
        $values = $handler->values();

        if (! is_array(collect($values)->first())) {
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
        return $this->getFilterHandlers()
            ->flatten()

            // Only filters which aren't in the request
            ->filter(function (Filter $handler) {
                return ! request()->has("filter_{$handler->getKey()}");
            })

            // Only required filters or retained filters with value saved in session
            ->filter(function (Filter $handler) {
                return $handler instanceof SelectRequiredFilter
                    || $handler instanceof DateRangeRequiredFilter
                    || $this->isRetainedFilter($handler, true);
            })

            ->map(function (Filter $handler) {
                if ($this->isRetainedFilter($handler, true)) {
                    return [
                        'name' => $handler->getKey(),
                        'value' => session("_sharp_retained_filter_{$handler->getKey()}"),
                    ];
                }

                return [
                    'name' => $handler->getKey(),
                    'value' => $handler->defaultValue(),
                ];
            })
            ->pluck('value', 'name')
            ->all();
    }

    /**
     * Save "retain" filter values in session. Retain filters
     * are those whose handler is defining a retainValueInSession()
     * function which returns true.
     */
    protected function putRetainedFilterValuesInSession(): void
    {
        $this->getFilterHandlers()
            ->flatten()
            // Only filters sent which are declared "retained"
            ->filter(function (Filter $handler) {
                return request()->has("filter_{$handler->getKey()}")
                    && $this->isRetainedFilter($handler);
            })
            ->each(function ($handler) {
                // Array case: we store a coma separated string
                // (to be consistent and only store strings on filter session)
                $value = is_array(request()->get("filter_{$handler->getKey()}"))
                    ? implode(',', request()->get("filter_{$handler->getKey()}"))
                    : request()->get("filter_{$handler->getKey()}");

                if (strlen(trim($value)) === 0) {
                    // No value, we have to unset the retained value
                    session()->forget("_sharp_retained_filter_{$handler->getKey()}");
                } else {
                    session()->put(
                        "_sharp_retained_filter_{$handler->getKey()}",
                        $value,
                    );
                }
            });

        session()->save();
    }

    protected function isRetainedFilter(Filter $handler, $onlyValued = false): bool
    {
        return $handler->isRetainInSession()
            && (! $onlyValued || session()->has("_sharp_retained_filter_{$handler->getKey()}"));
    }

    protected function isGlobalFilter(Filter $handler): bool
    {
        return $handler instanceof GlobalRequiredFilter;
    }

    /**
     * Return the filter default value, which can be, in that order:
     * - the retained value, if the filter is retained
     * - the default value is the filter is required
     * - or null.
     */
    protected function getFilterDefaultValue(Filter $handler): int|string|array|null
    {
        if ($this->isGlobalFilter($handler)) {
            return session("_sharp_retained_global_filter_{$handler->getKey()}") ?: $handler->defaultValue();
        }

        if ($this->isRetainedFilter($handler, true)) {
            $sessionValue = session("_sharp_retained_filter_{$handler->getKey()}");

            if ($handler instanceof SelectMultipleFilter) {
                return explode(',', $sessionValue);
            }

            if ($handler instanceof DateRangeFilter) {
                [$start, $end] = collect(explode('..', $sessionValue))
                    ->map(function ($date) {
                        if (strlen($date) == 8) {
                            // Date was stored with a Ymd format, we need Y-m-d
                            return sprintf('%s-%s-%s',
                                substr($date, 0, 4),
                                substr($date, 4, 2),
                                substr($date, 6, 2),
                            );
                        }

                        return $date;
                    })
                    ->toArray();

                return compact('start', 'end');
            }

            return $sessionValue;
        }

        if ($handler instanceof SelectRequiredFilter) {
            return $handler->defaultValue();
        }

        if ($handler instanceof DateRangeRequiredFilter) {
            return collect($handler->defaultValue())
                ->map->format('Y-m-d')->toArray();
        }

        return null;
    }
}
