<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Utils\Filters\HandleFilters;
use Illuminate\Support\Collection;

class GlobalFilterController extends ApiController
{
    use HandleFilters;

    public function getFilters(): array
    {
        return config("sharp.global_filters");
    }

    public function index()
    {
        return response()->json(
            tap([], function(&$filters) {
                $this->appendFiltersToConfig($filters);
            })
        );
    }

    public function update(string $filterName)
    {
        abort_if(is_null($handlerClass = config("sharp.global_filters.$filterName")), 404);

        // Ensure value is in the filter value-set
        $allowedFilterValues = collect($this->formatSelectFilterValues(app($handlerClass)));
        $value = $allowedFilterValues->where("id", request("value"))->first()
            ? request("value")
            : null;

        if($value) {
            session()->put(
                "_sharp_retained_global_filter_$filterName",
                $value
            );
        } else {
            session()->forget("_sharp_retained_global_filter_$filterName");
        }

        return response()->json(["ok" => true]);
    }
}