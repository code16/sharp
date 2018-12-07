<?php

namespace App\Sharp\Filters;

use Code16\Sharp\Dashboard\DashboardRequiredFilter;

class TravelsDashboardPeriodFilter implements DashboardRequiredFilter
{

    public function values()
    {
        $years = [];
        $currentYear = now()->year;

        for($i=0; $i<3; $i++) {
            $years[$currentYear] = ($currentYear-1) . " - " . $currentYear;
            $currentYear--;
        }

        return $years;
    }

    public function defaultValue()
    {
        return now()->year;
    }

    public function label()
    {
        return "Period";
    }
}