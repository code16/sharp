<?php

namespace App\Sharp\Filters;

use Code16\Sharp\Dashboard\DashboardDateRangeFilter;

class TravelsDashboardPeriodFilter implements DashboardDateRangeFilter
{
    public function label(): string
    {
        return "period";
    }
}