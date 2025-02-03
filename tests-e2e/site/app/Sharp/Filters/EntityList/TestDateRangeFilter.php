<?php

namespace App\Sharp\Filters\EntityList;

use Code16\Sharp\EntityList\Filters\EntityListDateRangeFilter;

class TestDateRangeFilter extends EntityListDateRangeFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Date Range');
    }
}
