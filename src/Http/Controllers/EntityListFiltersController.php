<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Utils\Entities\SharpEntityManager;

class EntityListFiltersController extends SharpProtectedController
{
    public function __construct(
        private readonly SharpEntityManager $entityManager,
    ) {
        parent::__construct();
    }

    public function store(string $entityKey)
    {
        sharp_check_ability('entity', $entityKey);

        // We have to get rid of the final /filters in the current URL
        // to prevent SharpBreadcrumb from considering it as a segment
        //  in case it is built in the functional code (buildListConfig() for instance)
        sharp()->context()->breadcrumb()->forceRequestSegments(
            collect(request()->segments())
                ->slice(1, -1)
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
