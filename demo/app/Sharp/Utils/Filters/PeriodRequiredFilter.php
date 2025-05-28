<?php

namespace App\Sharp\Utils\Filters;

use Code16\Sharp\Filters\DateRangeRequiredFilter;

class PeriodRequiredFilter extends DateRangeRequiredFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Period')->configureShowPresets();
    }

    public function defaultValue(): array
    {
        return [
            'start' => today()->subDays(30),
            'end' => today(),
        ];
    }
}
