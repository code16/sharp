<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Filters\GlobalFilters\GlobalFilters;
use Code16\Sharp\Filters\GlobalRequiredFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class HandleGlobalFilters
{
    public function __construct(private GlobalFilters $globalFiltersHandler) {}

    public function handle(Request $request, Closure $next)
    {
        if ($filterKey = $request->route('filterKey')) {
            $filterKeys = explode(GlobalFilters::$valuesUrlSeparator, $filterKey);

            if ($this->globalFiltersHandler->isEnabled()) {
                $globalFilters = $this->globalFiltersHandler->getFilters();
                if (count($filterKeys) !== count($globalFilters)) {
                    return redirect()->route('code16.sharp.home', [
                        'filterKey' => sharp()->context()->globalFilterUrlSegmentValue(),
                    ]);
                }

                collect($globalFilters)
                    ->each(fn (GlobalRequiredFilter $globalFilter, int $index) => $globalFilter
                        ->setCurrentValue($filterKeys[$index])
                    );
            }
        }

        URL::defaults(['filterKey' => sharp()->context()->globalFilterUrlSegmentValue()]);

        return $next($request);
    }
}
