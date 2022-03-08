<?php

namespace App\Sharp\Utils\Filters;

use Code16\Sharp\EntityList\Filters\EntityListDateRangeFilter;

class PeriodFilter extends EntityListDateRangeFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Period');
    }
}
