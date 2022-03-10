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

    public function fillWithRequest(): self
    {
        $query = request()->method() === "GET" ? request()->all() : request('query');

        $this->fillFilterWithRequest($query);

        return $this;
    }
}
