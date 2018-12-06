<?php

namespace Code16\Sharp\Dashboard;

use Code16\Sharp\Utils\Filters\HasFiltersInQuery;

class DashboardQueryParams
{
    use HasFiltersInQuery;

    /**
     * @return DashboardQueryParams
     */
    public static function create()
    {
        return new static;
    }

    /**
     * @param string|null $queryPrefix
     * @return $this
     */
    public function fillWithRequest(string $queryPrefix = null)
    {
        $query = $queryPrefix ? request($queryPrefix) : request()->all();

        $this->fillFilterWithRequest($query);

        return $this;
    }
}