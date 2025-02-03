<?php

namespace Code16\Sharp\Search;

use Illuminate\Support\Collection;
use Illuminate\Foundation\Auth\User as Authenticatable;

abstract class SharpSearchEngine
{
    protected array $resultSets = [];

    protected function addResultSet(string $label, ?string $icon = null): SearchResultSet
    {
        return tap(new SearchResultSet($label, $icon), function (SearchResultSet $resultSet) {
            $this->resultSets[] = $resultSet;
        });
    }

    final public function resultSets(): Collection
    {
        return collect($this->resultSets);
    }

    abstract public function searchFor(array $terms): void;

    public function authorizeFor(Authenticatable $user): bool
    {
        return true;
    }
}
