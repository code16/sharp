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
        $list = $this->entityManager->entityFor($entityKey)->getListOrFail();
        $list->buildListConfig();
        
        $list->putRetainedFilterValuesInSession(request()->input('filterValues'));
        
        return redirect()->route('code16.sharp.list', [
            'entityKey' => $entityKey,
            ...request()->input('query', []),
            ...$list->getFilterValuesQueryParams(request()->input('filterValues')),
            'page' => null,
        ]);
    }
}
