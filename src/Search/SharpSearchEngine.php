<?php

namespace Code16\Sharp\Search;

use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Collection;

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

    public function authorizeFor(User $user): bool
    {
        return true;
    }
}
