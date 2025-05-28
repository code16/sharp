<?php

namespace Code16\Sharp\Filters\FilterContainer\Concerns;

use Code16\Sharp\Filters\Filter;
use Illuminate\Support\Collection;

trait HandlesFiltersInSession
{
    public function getFilterValuesRetainedInSession(): Collection
    {
        return $this->getFilterHandlers()
            ->flatten()
            ->filter(fn (Filter $handler) => $this->isRetainedFilter($handler))
            ->mapWithKeys(fn (Filter $handler) => [
                $handler->getKey() => $handler
                    ->fromQueryParam(session('_sharp_retained_filter_'.$handler->getKey())),
            ]);
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
            ->filter(fn (Filter $handler) => $handler->isRetainInSession())
            ->each(function (Filter $handler) use ($filterValues) {
                $value = $handler->toQueryParam($filterValues[$handler->getKey()] ?? null);

                if ($value === null || $value === '') {
                    // No value, we have to unset the retained value
                    session()->forget('_sharp_retained_filter_'.$handler->getKey());
                } else {
                    session()->put(
                        '_sharp_retained_filter_'.$handler->getKey(),
                        $value,
                    );
                }
            });

        session()->save();
    }

    public function isRetainedFilter(Filter $handler): bool
    {
        return $handler->isRetainInSession()
            && session()->has('_sharp_retained_filter_'.$handler->getKey());
    }
}
