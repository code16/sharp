<?php

namespace App\Sharp\Filters\EntityList;

use Code16\Sharp\Filters\DateRangeRequiredFilter;

class TestDateRangeRequiredFilter extends DateRangeRequiredFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Date Range required');
    }

    public function defaultValue(): array
    {
        return [
            'start' => '2021-01-01',
            'end' => '2021-01-31',
        ];
    }
}
