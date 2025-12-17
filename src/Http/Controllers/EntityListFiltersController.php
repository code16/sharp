<?php

namespace Code16\Sharp\Http\Controllers;

class EntityListFiltersController extends SharpProtectedController
{
    public function store(string $globalFilter, string $entityKey)
    {
        $this->authorizationManager->check('entity', $entityKey);

        // We have to get rid of the final /filters in the current URL
        // to prevent SharpBreadcrumb from considering it as a segment
        //  in case it is built in the functional code (buildListConfig() for instance)
        sharp()->context()->breadcrumb()->forceRequestSegments(
            collect(request()->segments())
                ->slice(2, -1)
                ->values()
        );

        $list = $this->entityManager->entityFor($entityKey)->getListOrFail();
        $list->buildListConfig();

        $list->filterContainer()->putRetainedFilterValuesInSession(request()->input('filterValues'));

        return redirect()->route('code16.sharp.list', [
            'entityKey' => $entityKey,
            ...(request()->input('query') ?? []),
            ...$list->filterContainer()->getQueryParamsFromFilterValues(request()->input('filterValues')),
            'page' => null,
        ]);
    }
}
