<?php

namespace App\Sharp\Filters;

use Carbon\Carbon;
use Code16\Sharp\EntityList\Filters\EntityListDateRangeRequiredFilter;

class PassengerBirthdateFilter extends EntityListDateRangeRequiredFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Born between')
            ->configureDateFormat('YYYY-MM-DD')
            ->configureMondayFirst(false)
            ->configureRetainInSession();
    }

    public function defaultValue(): array
    {
        return [
            'start' => (new Carbon())->setDate(2014, 1, 1),
            'end'   => (new Carbon())->setDate(2014, 12, 31),
        ];
    }
}
