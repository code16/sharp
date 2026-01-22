<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Filters\GlobalFilters\GlobalFilters;
use Code16\Sharp\Filters\GlobalRequiredFilter;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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
                    return $this->redirectTo($request);
                }

                collect($globalFilters)
                    ->each(fn (GlobalRequiredFilter $globalFilter, int $index) => $globalFilter
                        ->setCurrentValue($globalFilterValues[$index])
                    );

                if (sharp()->context()->globalFilterUrlSegmentValue() !== $globalFilterValue
                    && ! $request->wantsJson()
                    && $request->isMethod('GET')
                ) {
                    return $this->redirectTo($request);
                }
            }
        }

        URL::defaults(['globalFilter' => sharp()->context()->globalFilterUrlSegmentValue()]);

        return $next($request);
    }

    protected function redirectTo(Request $request)
    {
        return $request->route()?->hasParameter('globalFilter')
            ? redirect()->route($request->route()->getName(), [
                'globalFilter' => sharp()->context()->globalFilterUrlSegmentValue(),
                ...Arr::except($request->route()->parameters(), 'globalFilter'),
                ...$request->query(),
            ])
            : redirect()->route('code16.sharp.home', [
                'globalFilter' => sharp()->context()->globalFilterUrlSegmentValue(),
            ]);
    }
}
