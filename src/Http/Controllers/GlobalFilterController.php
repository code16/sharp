<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Utils\Filters\GlobalFilters;
use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;
use Illuminate\Http\RedirectResponse;

class GlobalFilterController extends SharpProtectedController
{
    public function update(string $filterKey, GlobalFilters $globalFilters): RedirectResponse
    {
        $handler = $globalFilters->findFilter($filterKey);

        abort_if(! $handler instanceof GlobalRequiredFilter, 404);

        // Ensure value is in the filter value-set
        $value = request('value')
            ? collect($globalFilters->filterContainer()->formatSelectFilterValues($handler))
                ->where('id', request('value'))
                ->first()
            : null;

        $handler->setCurrentValue($value ? $value['id'] : null);

        return redirect()->route('code16.sharp.home');
    }

}
