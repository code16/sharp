<?php

namespace Code16\Sharp\Http\Controllers;

use App\Http\Kernel;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Http\Request;

class EntityListFiltersController extends SharpProtectedController
{
    public function __construct(
        private readonly SharpEntityManager $entityManager,
    ) {
        parent::__construct();
    }
    
    public function store(string $entityKey, Kernel $kernel)
    {
        sharp_check_ability('entity', $entityKey);
        
        $list = $this->entityManager->entityFor($entityKey)->getListOrFail();
        $list->buildListConfig();
        
        $list->filterContainer()->putRetainedFilterValuesInSession(request()->input('filterValues'));
        
        return redirect()->route('code16.sharp.list', [
            'entityKey' => $entityKey,
            ...request()->input('query', []),
            ...$list->filterContainer()->getQueryParamsFromFilterValues(request()->input('filterValues')),
            'page' => null,
        ]);
    }
}
