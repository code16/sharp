<?php

namespace App\Sharp\Filters;

use Code16\Sharp\Filters\DateRangeFilter;

class TestDateRangeFilter extends DateRangeFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Date Range');
    }
}
