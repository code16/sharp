<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Filters\GlobalFilters\GlobalFilters;
use Illuminate\Http\RedirectResponse;

class GlobalFilterController extends SharpProtectedController
{
    public function update(string $filterKey, GlobalFilters $globalFilters): RedirectResponse
    {
        collect(request()->input('filterValues'))
            ->each(function ($value, $key) use ($globalFilters) {
                $globalFilters->findFilter($key)->setCurrentValue($value);
            });

        return redirect()->route('code16.sharp.home', [
            'filterKey' => sharp()->context()->globalFilterUrlSegmentValue(),
        ]);
    }
}
