<?php

namespace Code16\Sharp\Dashboard;

use Code16\Sharp\Utils\Filters\HasFiltersInQuery;

class DashboardQueryParams
{
    use HasFiltersInQuery;

    public static function create(): self
    {
        return new static;
    }

    public function fillWithRequest(string $queryPrefix = null): self
    {
        $query = $queryPrefix ? request($queryPrefix) : request()->all();

        $this->fillFilterWithRequest($query);

        return $this;
    }
}