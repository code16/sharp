<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Http\Controllers\Controller;

class ApiEntityListFiltersController extends Controller
{
    public function store(string $globalFilter, string $entityKey)
    {
        $this->authorizationManager->check('entity', $entityKey);

        $list = $this->entityManager->entityFor($entityKey)->getListOrFail();
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
