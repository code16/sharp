<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Filters\GlobalRequiredFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class HandleGlobalFilters
{
    public function handle(Request $request, Closure $next)
    {
        URL::defaults(['filterKey' => sharp()->context()->globalFilterUrlSegmentValue()]);

        if ($request->isMethod('GET') && ($filterKey = $request->route('filterKey'))) {
            $filterKeys = explode(':', $filterKey);
            $configuredGlobalFilters = collect(sharp()->config()->get('global_filters'))
                ->map(fn ($globalFilterClassOrInstance) => is_string($globalFilterClassOrInstance)
                    ? app($globalFilterClassOrInstance)
                    : $globalFilterClassOrInstance
                );

            if (count($configuredGlobalFilters) != 0 && count($filterKeys) != count($configuredGlobalFilters)) {
                return redirect()->route('code16.sharp.home');
            }

            $configuredGlobalFilters
                ->each(fn (GlobalRequiredFilter $globalFilter, $index) => $globalFilter
                    ->setCurrentValue($filterKeys[$index])
                );
        }

        return $next($request);
    }
}
