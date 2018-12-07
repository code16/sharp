<?php

namespace App\Sharp\Filters;

use Code16\Sharp\Dashboard\DashboardRequiredFilter;

class TravelsDashboardPeriodFilter implements DashboardRequiredFilter
{

    public function values()
    {
        return [
            1 => "+/- 1 year",
            3 => "+/- 3 years",
            5 => "+/- 5 years",
            10 => "+/- 10 years",
        ];
    }

    public function defaultValue()
    {
        return 5;
    }

    public function label()
    {
        return "Period";
    }
}