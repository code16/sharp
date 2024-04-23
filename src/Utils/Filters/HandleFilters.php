<?php

namespace Code16\Sharp\Utils\Filters;

use Code16\Sharp\EntityList\Filters\HiddenFilter;
use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Support\Collection;

trait HandleFilters
{
    public ?FilterContainer $filterContainer = null;
    
    final public function getFilterValuesToFront(): array
    {
        return [
            'default' => $defaultValues = $this->filterContainer->getFilterHandlers()
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
        $this->filterContainer
            ->getFilterHandlers()
            ->each(function (Collection $filterHandlers, string $positionKey) use (&$config) {
                if ($filterHandlers->count() === 0) {
                    return;
                }

                $config['filters'][$positionKey] = $filterHandlers
                    ->filter(fn (Filter $handler) => ! $this->isHiddenFilter($handler))
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

    /**
     * @internal
     */
    final public function getFilterHandlers(): Collection
    {
        if ($this->filterContainer === null) {
            $this->filterContainer = new FilterContainer($this->getFilters());
        }

        return $this->filterContainer->getFilterHandlers();
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
                    || $this->isRetainedFilter($handler);
            })

            ->map(function (Filter $handler) {
                if ($this->isRetainedFilter($handler)) {
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

    protected function isRetainedFilter(Filter $handler): bool
    {
        return $handler->isRetainInSession()
            && session()->has("_sharp_retained_filter_{$handler->getKey()}");
    }

    protected function isHiddenFilter(Filter $handler): bool
    {
        return $handler instanceof HiddenFilter;
    }
}
