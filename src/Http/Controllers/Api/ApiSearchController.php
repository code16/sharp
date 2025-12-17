<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Data\SearchResultSetData;
use Code16\Sharp\Search\SearchResultSet;
use Code16\Sharp\Utils\StringUtil;

class ApiSearchController extends ApiController
{
    public function index(string $filterKey)
    {
        $searchEngine = sharp()->config()->get('search.engine');

        abort_if(! $searchEngine, 404);
        abort_if(! $searchEngine->authorize(), 403);

        $searchEngine->searchFor(
            app(StringUtil::class)
                ->explodeSearchTerms(request()->input('q', ''))
                ->all()
        );

        return response()->json(
            SearchResultSetData::collection(
                $searchEngine
                    ->resultSets()
                    ->map(fn (SearchResultSet $resultSet) => $resultSet->toArray())
                    ->all()
            ),
        );
    }
}
