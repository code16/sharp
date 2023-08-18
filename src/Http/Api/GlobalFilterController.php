<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Utils\Filters\Filter;
use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;
use Code16\Sharp\Utils\Filters\HandleFilters;

class GlobalFilterController extends ApiController
{
    use HandleFilters;

    public function getFilters(): array
    {
        return value(config('sharp.global_filters'));
    }

    public function index()
    {
        return response()->json(
            tap([], function (&$config) {
                $this->appendFiltersToConfig($config);
            }),
        );
    }

    public function update(string $filterKey)
    {
        $handler = collect($this->getFilters())
            ->map(fn (string $filterClass) => app($filterClass))
            ->filter(fn (Filter $filter) => $filter->getKey() == $filterKey)
            ->first();

        abort_if(! $handler instanceof GlobalRequiredFilter, 404);

        // Ensure value is in the filter value-set
        $value = request('value')
            ? collect($this->formatSelectFilterValues($handler))
                ->where('id', request('value'))
                ->first()
            : null;

        $handler->setCurrentValue($value ? $value['id'] : null);

        return response()->json(['ok' => true]);
    }
}
