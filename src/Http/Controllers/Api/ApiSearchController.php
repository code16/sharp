<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Data\SearchResultSetData;
use Code16\Sharp\Search\SearchResultSet;
use Code16\Sharp\Search\SharpSearchEngine;
use Code16\Sharp\Utils\StringUtil;

class ApiSearchController extends ApiController
{
    public function index()
    {
        $searchEngine = tap(
            sharp()->config()->get('search.engine'),
            function (SharpSearchEngine $engine) {
                if ($engine->authorizeFor(auth()->user())) {
                    $engine->searchFor(
                        app(StringUtil::class)
                            ->explodeSearchTerms(request()->input('q', ''))
                            ->all()
                    );
                }
            }
        );

        return response()->json(
            SearchResultSetData::collection(
                $searchEngine->resultSets()
                    ->map(fn (SearchResultSet $resultSet) => $resultSet->toArray())
                    ->all()
            ),
        );
    }

    //    private function getSearchEngine(): SharpSearchEngine
    //    {
    //        $searchEngine = value(config('sharp.search.engine'));
    //
    //        return is_string($searchEngine)
    //            ? app($searchEngine)
    //            : $searchEngine;
    //    }
}
