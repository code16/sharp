<?php

namespace Code16\Sharp\Search;

abstract class SharpSearchEngine
{
    protected array $resultSets = [];
    
    protected function addResultSet(string $label, ?string $icon = null): SearchResultSet
    {
        return tap(new SearchResultSet($label, $icon), function (SearchResultSet $resultSet) {
            $this->resultSets[] = $resultSet;
        });
    }
}