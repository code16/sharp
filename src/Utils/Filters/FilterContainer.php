<?php

namespace Code16\Sharp\Utils\Filters;

use Code16\Sharp\EntityList\Filters\HiddenFilter;
use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Support\Collection;

class FilterContainer
{
    protected ?Collection $filterHandlers = null;
    
    public function __construct(
        protected ?array $baseFilters = null,
    ) {
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
    
    /**
     * @internal
     */
    final public function getFilterHandlers(): Collection
    {
        if ($this->filterHandlers === null) {
            $this->filterHandlers = collect($this->baseFilters)
                
                // First get filters which are section-based (dashboard only)...
                ->filter(fn ($value, $index) => is_array($value))
                
                // ... and merge filters set for the whole page
                ->merge(
                    [
                        '_root' => collect($this->baseFilters)
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
