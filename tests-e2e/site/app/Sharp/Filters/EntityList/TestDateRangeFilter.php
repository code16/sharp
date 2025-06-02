<?php

namespace App\Sharp\Filters\EntityList;

use Code16\Sharp\Filters\DateRangeFilter;

class TestDateRangeFilter extends DateRangeFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Date Range');
    }
}
