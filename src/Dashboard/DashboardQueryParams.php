<?php

namespace Code16\Sharp\Dashboard;

use Code16\Sharp\Utils\Filters\HasFiltersInQuery;
use Illuminate\Support\Collection;

class DashboardQueryParams
{
    use HasFiltersInQuery;

    public static function create(Collection $filterHandlers): static
    {
        return tap(
            new static,
            fn (DashboardQueryParams $instance) => $instance->filterHandlers = $filterHandlers
        );
    }

    public function fillWithRequest(): self
    {
        $query = request()->method() === 'GET' ? request()->all() : request('query');

        $this->fillFilterWithRequest($query);

        return $this;
    }
}
