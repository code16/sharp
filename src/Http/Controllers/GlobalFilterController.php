<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Exceptions\SharpInvalidFilterValueException;
use Code16\Sharp\Filters\GlobalFilters\GlobalFilters;
use Code16\Sharp\Filters\GlobalRequiredFilter;
use Illuminate\Http\RedirectResponse;

class GlobalFilterController extends SharpProtectedController
{
    public function update(string $filterKey, GlobalFilters $globalFilters): RedirectResponse
    {
        $handler = $globalFilters->findFilter($filterKey);

        abort_if(! $handler instanceof GlobalRequiredFilter, 404);

        try {
            $handler->setCurrentValue(request('value'));
        } catch (SharpInvalidFilterValueException) {
            // Reset global filter to its previous value instead of showing an error
        }

        return redirect()->route('code16.sharp.home');
    }
}
