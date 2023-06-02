<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Search\SearchResultSet;
use Code16\Sharp\Search\SharpSearchEngine;
use Code16\Sharp\Utils\StringUtil;

class SearchController extends ApiController
{
    public function index()
    {
        $searchEngine = tap(
            $this->getSearchEngine(), 
            fn (SharpSearchEngine $engine) => $engine->searchFor(
                app(StringUtil::class)
                    ->explodeSearchTerms(request()->input('q'))
                    ->all()
            )
        );
        
        return response()->json([
            $searchEngine->resultSets()
                ->map(fn (SearchResultSet $resultSet) => $resultSet->toArray())
                ->all()
        ]);
    }

    private function getSearchEngine(): SharpSearchEngine
    {
        return app(value(config('sharp.search.engine')));
    }
}
