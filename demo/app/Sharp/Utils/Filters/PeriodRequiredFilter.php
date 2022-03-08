<?php

namespace App\Sharp\Utils\Filters;

use Code16\Sharp\Dashboard\Filters\DashboardDateRangeRequiredFilter;

class PeriodRequiredFilter extends DashboardDateRangeRequiredFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Period');
    }

    public function defaultValue(): array
    {
        return [
            'start' => today()->subDays(30),
            'end' => today()->subDay(),
        ];
    }
}
