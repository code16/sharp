<?php

namespace App\Sharp\Filters;

use Code16\Sharp\Dashboard\Filters\DashboardDateRangeFilter;

class TravelsDashboardPeriodFilter extends DashboardDateRangeFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('period');
    }
}
