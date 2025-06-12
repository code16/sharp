<?php

namespace App\Sharp\Utils\Filters;

use Code16\Sharp\Filters\DateRange\DateRangePreset;
use Code16\Sharp\Filters\DateRangeRequiredFilter;

class PeriodRequiredFilter extends DateRangeRequiredFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Period')
            ->configureShowPresets(presets: [
                DateRangePreset::make(today()->subDays(3), today(), 'Last 3 days'),
                DateRangePreset::thisMonth(),
                DateRangePreset::thisYear(),
            ]);
    }

    public function defaultValue(): array
    {
        return [
            'start' => today()->subDays(30),
            'end' => today(),
        ];
    }
}
