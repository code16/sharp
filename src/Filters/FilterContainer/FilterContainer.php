<?php

namespace Code16\Sharp\Filters\FilterContainer;

use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Filters\DateRangeRequiredFilter;
use Code16\Sharp\Filters\Filter;
use Code16\Sharp\Filters\FilterContainer\Concerns\BuildsFiltersConfigArray;
use Code16\Sharp\Filters\FilterContainer\Concerns\HandlesFiltersInQueryParams;
use Code16\Sharp\Filters\FilterContainer\Concerns\HandlesFiltersInSession;
use Code16\Sharp\Filters\FilterContainer\Concerns\ProvidesFilterValuesToFront;
use Code16\Sharp\Filters\SelectRequiredFilter;
use Illuminate\Support\Collection;

class FilterContainer
{
    use BuildsFiltersConfigArray;
    use HandlesFiltersInQueryParams;
    use HandlesFiltersInSession;
    use ProvidesFilterValuesToFront;

    protected ?Collection $filterHandlers = null;
    protected array $excludedFilters = [];

    public function __construct(
        protected ?array $baseFilters = null,
    ) {}

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

                ->map(fn ($handlers) => collect($handlers)
                    ->map(function ($filterHandlerOrClassName) {
                        $filterHandler = instanciate($filterHandlerOrClassName);

                        if (! $filterHandler instanceof Filter) {
                            throw new SharpException(sprintf(
                                'Handler class for filter [%s] must implement a sub-interface of [%s]',
                                $filterHandlerOrClassName,
                                Filter::class
                            ));
                        }

                        $filterHandler->buildFilterConfig();

                        return $filterHandler;
                    })
                    ->filter(fn (Filter $filter) => ! in_array($filter->getKey(), $this->excludedFilters))
                    ->values()
                );
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

    public function excludeFilter(string $filterClassNameOrKey): void
    {
        if ($filter = $this->findFilterHandler($filterClassNameOrKey)) {
            $this->excludedFilters[] = $filter->getKey();
        }

        $this->filterHandlers = null;
    }
}
