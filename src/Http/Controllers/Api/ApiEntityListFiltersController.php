<?php

namespace Code16\Sharp\Http\Controllers\Api;

class ApiEntityListFiltersController extends ApiController
{
    public function store(string $entityKey)
    {
        sharp_check_ability('entity', $entityKey);

        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        $list->filterContainer()
            ->putRetainedFilterValuesInSession(
                collect(request()->input('filterValues', []))
                    ->diffKeys(request()->input('hiddenFilters') ?? [])
                    ->toArray()
            );

        return redirect()->route('code16.sharp.api.list', [
            'entityKey' => $entityKey,
            ...(request()->input('query') ?? []),
            ...$list->filterContainer()->getQueryParamsFromFilterValues([
                ...request()->input('filterValues', []),
                ...(request()->input('hiddenFilters') ?? []),
            ]),
            'page' => null,
        ]);
    }
}
