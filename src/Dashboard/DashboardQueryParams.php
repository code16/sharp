<?php

namespace Code16\Sharp\Dashboard;

use Code16\Sharp\Filters\Concerns\HasFiltersInQuery;
use Code16\Sharp\Filters\FilterContainer\FilterContainer;

class DashboardQueryParams
{
    use HasFiltersInQuery;

    public function __construct(
        protected FilterContainer $filterContainer,
        protected array $filterValues = [],
    ) {}

    public function fillWithRequest(): self
    {
        $query = request()->method() === 'GET' ? request()->all() : request('query');

        $this->fillFilterWithRequest($query);

        return $this;
    }
}
