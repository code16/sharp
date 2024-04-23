<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Exceptions\SharpMethodNotImplementedException;

class ApiEntityListFiltersController extends ApiController
{
    public function store(string $entityKey)
    {
        sharp_check_ability('entity', $entityKey);

        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        
        $list->putRetainedFilterValuesInSession(
            collect(request()->input('filterValues', []))
                ->diffKeys(request()->input('hiddenFilters', []))
                ->toArray()
        );
        
        return redirect()->route('code16.sharp.api.list', [
            'entityKey' => $entityKey,
            ...request()->input('query', []),
            ...$list->getFilterValuesQueryParams(request()->input('filterValues', [])),
            'page' => null,
        ]);
    }
}
