<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Utils\Filters\Filter;
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

        abort_if(! $handler, 404);

        // Ensure value is in the filter value-set
        $value = collect($this->formatSelectFilterValues($handler))
            ->where('id', request('value'))
            ->first() ? request('value') : null;

        if ($value) {
            session()->put(
                "_sharp_retained_global_filter_{$handler->getKey()}",
                $value,
            );
        } else {
            session()->forget("_sharp_retained_global_filter_{$handler->getKey()}");
        }

        return response()->json(['ok' => true]);
    }
}
