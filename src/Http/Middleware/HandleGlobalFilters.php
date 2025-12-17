<?php

namespace Code16\Sharp\Http\Middleware;

use Closure;
use Code16\Sharp\Filters\GlobalFilters\GlobalFilters;
use Code16\Sharp\Filters\GlobalRequiredFilter;
use Code16\Sharp\Http\Context\SharpBreadcrumb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class HandleGlobalFilters
{
    public function __construct(private GlobalFilters $globalFiltersHandler) {}

    public function handle(Request $request, Closure $next)
    {
        $globalFilterValue = $request->route('filterKey');
        if (! $globalFilterValue && $request->wantsJson()) {
            if ($urlToParse = $request->header(SharpBreadcrumb::CURRENT_PAGE_URL_HEADER) ?: request()->query('current_page_url')) {
                $globalFilterValue = str($urlToParse)
                    ->after($request->host().'/'.sharp()->config()->get('custom_url_segment').'/')
                    ->before('/');
            }
        }

        if ($globalFilterValue) {
            $globalFilterValues = explode(GlobalFilters::$valuesUrlSeparator, $globalFilterValue);

            if ($this->globalFiltersHandler->isEnabled()) {
                $globalFilters = $this->globalFiltersHandler->getFilters();
                if (count($globalFilterValues) !== count($globalFilters)) {
                    return redirect()->route('code16.sharp.home', [
                        'filterKey' => sharp()->context()->globalFilterUrlSegmentValue(),
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
                    // Filter value is invalid, redirect to homepage
                    return redirect()->route('code16.sharp.home', [
                        'filterKey' => sharp()->context()->globalFilterUrlSegmentValue(),
                    ]);
                }
            }
        }

        URL::defaults(['filterKey' => sharp()->context()->globalFilterUrlSegmentValue()]);

        return $next($request);
    }
}
