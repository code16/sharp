<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Filters\GlobalRequiredFilter;
use Illuminate\Support\Facades\URL;

class HandleGlobalFilters
{
    public function handle($request, Closure $next)
    {
        $globalFilterValues = collect(sharp()->config()->get('global_filters'))
            ->map(fn ($globalFilterClassOrInstance) => is_string($globalFilterClassOrInstance) ? app($globalFilterClassOrInstance) : $globalFilterClassOrInstance)
            ->map(fn (GlobalRequiredFilter $globalFilter) => $globalFilter->currentValue())
            ->filter();

        URL::defaults(['filterKey' => $globalFilterValues->isEmpty() ? 'root' : $globalFilterValues->implode('-')]);

        return $next($request);
    }
}
