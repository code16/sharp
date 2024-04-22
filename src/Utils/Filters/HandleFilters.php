<?php

namespace Code16\Sharp\Utils\Filters;

use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Support\Collection;

trait HandleFilters
{
    protected ?Collection $filterHandlers = null;
    
    final public function getFilterValuesToFront(): array
    {
        return [
            'default' => $defaultValues = $this->getFilterHandlers()
                ->flatten()
                ->mapWithKeys(function (Filter $handler) {
                    if ($handler instanceof SelectRequiredFilter
                        || $handler instanceof DateRangeRequiredFilter
                    ) {
                        return [$handler->getKey() => $this->formatFilterValueToFront($handler, $handler->defaultValue())];
                    }
                    return [$handler->getKey() => null];
                })
                ->toArray(),
            'current' => $this->getFilterHandlers()
                ->flatten()
                ->mapWithKeys(function (Filter $handler) use ($defaultValues) {
                    $value = $this->formatFilterValueToFront(
                        $handler,
                        $handler->fromQueryParam(
                            $this->queryParams->getFilterValues()[$handler->getKey()] ?? null
                        )
                    );
                    
                    return [$handler->getKey() => $value ?? $defaultValues[$handler->getKey()]];
                })
                ->toArray(),
        ];
    }
    
    final public function getFilterValuesQueryParams(array $filterValues): array
    {
        return $this->getFilterHandlers()
            ->flatten()
            ->mapWithKeys(function (Filter $handler) use ($filterValues) {
                $value = $handler->toQueryParam($filterValues[$handler->getKey()] ?? null);
                return [
                    'filter_'.$handler->getKey() => $value,
                ];
            })
            ->toArray();
    }
    
    /**
     * Save "retain" filter values in session. Retain filters
     * are those whose handler is defining a retainValueInSession()
     * function which returns true.
     */
    final public function putRetainedFilterValuesInSession(array $filterValues): void
    {
        $this->getFilterHandlers()
            ->flatten()
            // Only filters sent which are declared "retained"
            ->filter(function (Filter $handler) {
                return $handler->isRetainInSession();
            })
            ->each(function (Filter $handler) use ($filterValues) {
                $value = $handler->toQueryParam($filterValues[$handler->getKey()] ?? null);
                
                if ($value === null || $value === '') {
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
    
    protected function formatFilterValueToFront(Filter $handler, mixed $value)
    {
        if ($value && $handler instanceof DateRangeFilter) {
            $value = [
                'start' => $value['start']->format('Y-m-d'),
                'end' => $value['end']->format('Y-m-d'),
            ];
        }
        return $value;
    }

    protected function appendFiltersToConfig(array &$config): void
    {
        $this->getFilterHandlers()
            ->each(function (Collection $filterHandlers, string $positionKey) use (&$config) {
                if ($filterHandlers->count() === 0) {
                    return;
                }

                $config['filters'][$positionKey] = $filterHandlers
                    ->map(function (Filter $filterHandler) {
                        $filterConfigData = [
                            'key' => $filterHandler->getKey(),
                            'label' => $filterHandler->getLabel(),
                        ];

                        if ($filterHandler instanceof SelectFilter) {
                            $multiple = $filterHandler instanceof SelectMultipleFilter;

                            $filterConfigData += [
                                'type' => 'select',
                                'multiple' => $multiple,
                                'required' => ! $multiple && $filterHandler instanceof SelectRequiredFilter,
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

                // First get filters which are section-based (dashboard only)...
                ->filter(fn ($value, $index) => is_array($value))

                // ... and merge filters set for the whole page
                ->merge(
                    [
                        '_root' => collect($this->getFilters())
                            ->filter(fn ($value, $index) => ! is_array($value)),
                    ]
                )

                ->map(function ($handlers) {
                    return collect($handlers)
                        ->map(function ($filterHandlerOrClassName) {
                            if (is_string($filterHandlerOrClassName)) {
                                if (! class_exists($filterHandlerOrClassName)) {
                                    throw new SharpException(sprintf(
                                        'Handler for filter [%s] is invalid',
                                        $filterHandlerOrClassName
                                    ));
                                }
                                $filterHandler = app($filterHandlerOrClassName);
                            } else {
                                $filterHandler = $filterHandlerOrClassName;
                            }

                            if (! $filterHandler instanceof Filter) {
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

            // Only filters which aren't *valuated* in the request
            ->filter(fn (Filter $handler) => ! request()->get('filter_'.$handler->getKey()))

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

    protected function isRetainedFilter(Filter $handler, $onlyValued = false): bool
    {
        return $handler->isRetainInSession()
            && (! $onlyValued || session()->has("_sharp_retained_filter_{$handler->getKey()}"));
    }

    protected function isGlobalFilter(Filter $handler): bool
    {
        return $handler instanceof GlobalRequiredFilter;
    }
}
