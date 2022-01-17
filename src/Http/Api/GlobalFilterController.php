<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Utils\Filters\Filter;
use Code16\Sharp\Utils\Filters\HandleFilters;

class GlobalFilterController extends ApiController
{
    use HandleFilters;

    public function getFilters(): array
    {
        return config('sharp.global_filters');
    }

    public function index()
    {
        return response()->json(
            tap([], function (&$filters) {
                $this->appendFiltersToConfig($filters);
            })
        );
    }

    public function update(string $filterKey)
    {
        $handler = collect(config('sharp.global_filters'))
            ->map(function (string $filterClass) {
                return app($filterClass);
            })
            ->filter(function (Filter $filter) use ($filterKey) {
                return $filter->getKey() == $filterKey;
            })
            ->first();

        abort_if(!$handler, 404);

        // Ensure value is in the filter value-set
        $allowedFilterValues = collect($this->formatSelectFilterValues($handler));
        $value = $allowedFilterValues->where('id', request('value'))->first()
            ? request('value')
            : null;

        if ($value) {
            session()->put(
                "_sharp_retained_global_filter_{$handler->getKey()}",
                $value
            );
        } else {
            session()->forget("_sharp_retained_global_filter_{$handler->getKey()}");
        }

        return response()->json(['ok' => true]);
    }
}
