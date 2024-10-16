<?php

namespace Code16\Sharp\Utils\Filters;

use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Utils\Filters\Concerns\BuildsFiltersConfigArray;
use Code16\Sharp\Utils\Filters\Concerns\HandlesFiltersInQueryParams;
use Code16\Sharp\Utils\Filters\Concerns\HandlesFiltersInSession;
use Code16\Sharp\Utils\Filters\Concerns\ProvidesFilterValuesToFront;
use Illuminate\Support\Collection;

class FilterContainer
{
    use BuildsFiltersConfigArray;
    use HandlesFiltersInSession;
    use HandlesFiltersInQueryParams;
    use ProvidesFilterValuesToFront;
    
    protected ?Collection $filterHandlers = null;
    
    public function __construct(
        protected ?array $baseFilters = null,
    ) {
    }
    
    public function getFilterHandlers(): Collection
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
    
    public function findFilterHandler(string $filterFullClassNameOrKey): ?Filter
    {
        return $this
            ->getFilterHandlers()
            ->flatten()
            ->filter(function (Filter $filter) use ($filterFullClassNameOrKey) {
                if (class_exists($filterFullClassNameOrKey)) {
                    return $filter instanceof $filterFullClassNameOrKey;
                }
                return $filter->getKey() === $filterFullClassNameOrKey;
            })
            ->first();
    }
    
    public function formatSelectFilterValues(SelectFilter $handler): array
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
    
    public function getCurrentFilterValues(?array $query): array
    {
        return [
            ...$this->getDefaultFilterValues(),
            ...$this->getFilterValuesRetainedInSession(),
            ...$this->getFilterValuesFromQueryParams($query),
        ];
    }
    
    public function getDefaultFilterValues(): Collection
    {
        return $this->getFilterHandlers()
            ->flatten()
            ->whereInstanceOf([
                SelectRequiredFilter::class,
                DateRangeRequiredFilter::class,
            ])
            ->mapWithKeys(function (Filter $handler) {
                return [
                    // we pass through fromQueryParam() & toQueryParam() to ensure the value is formatted correctly
                    $handler->getKey() => $handler->fromQueryParam($handler->toQueryParam($handler->defaultValue())),
                ];
            });
    }
}
