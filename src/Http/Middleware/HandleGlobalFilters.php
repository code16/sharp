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
        if ($globalFilterValue = $request->route('globalFilter')) {
            $globalFilterValues = explode(GlobalFilters::$valuesUrlSeparator, $globalFilterValue);

            if ($this->globalFiltersHandler->isEnabled()) {
                $globalFilters = $this->globalFiltersHandler->getFilters();
                if (count($globalFilterValues) !== count($globalFilters)) {
                    return redirect()->route('code16.sharp.home', [
                        'globalFilter' => sharp()->context()->globalFilterUrlSegmentValue(),
                    ]);
                }

                collect($globalFilters)
                    ->each(fn (GlobalRequiredFilter $globalFilter, int $index) => $globalFilter
                        ->setCurrentValue($globalFilterValues[$index])
                    );

                if (sharp()->context()->globalFilterUrlSegmentValue() !== $globalFilterValue
                    && ! $request->wantsJson()
                    && $request->isMethod('GET')
                ) {
                    return redirect()->route('code16.sharp.home', [
                        'globalFilter' => sharp()->context()->globalFilterUrlSegmentValue(),
                    ]);
                }
            }
        }

        URL::defaults(['globalFilter' => sharp()->context()->globalFilterUrlSegmentValue()]);

        return $next($request);
    }
}
