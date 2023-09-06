<?php

namespace Code16\Sharp\Http;

use Code16\Sharp\Utils\Filters\Filter;
use Code16\Sharp\Utils\Filters\GlobalFilters;
use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;
use Code16\Sharp\Utils\Filters\HandleFilters;
use Code16\Sharp\Utils\Menu\SharpMenuItem;
use Code16\Sharp\Utils\Menu\SharpMenuManager;
use Illuminate\Http\RedirectResponse;

class GlobalFilterController extends SharpProtectedController
{
    use HandleFilters;

    public function update(string $filterKey): RedirectResponse
    {
        $handler = app(GlobalFilters::class)->findFilter($filterKey);

        abort_if(! $handler instanceof GlobalRequiredFilter, 404);

        // Ensure value is in the filter value-set
        $value = request('value')
            ? collect($this->formatSelectFilterValues($handler))
                ->where('id', request('value'))
                ->first()
            : null;

        $handler->setCurrentValue($value ? $value['id'] : null);

        return redirect()->route('code16.sharp.home');
    }
}
