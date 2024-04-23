<?php

namespace Code16\Sharp\Http\Controllers;


use Code16\Sharp\Utils\Filters\DateRangeFilter;
use Code16\Sharp\Utils\Filters\DateRangeRequiredFilter;
use Code16\Sharp\Utils\Filters\Filter;
use Code16\Sharp\Utils\Filters\SelectRequiredFilter;
use Illuminate\Support\Collection;

trait HandlesFilters
{
    public function getFilterValuesToFront(Collection $filterHandlers): array
    {
        return [
            'default' => $defaultValues = $filterHandlers
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
            'current' => $filterHandlers
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
    
    public function getFilterValuesQueryParams(array $filterValues): array
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
    public function putRetainedFilterValuesInSession(array $filterValues): void
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
}
