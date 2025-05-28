<?php

namespace App\Sharp\Utils\Filters;

use Code16\Sharp\Filters\DateRangeFilter;

class PeriodFilter extends DateRangeFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Period');
    }
}
