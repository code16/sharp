<?php

namespace App\Sharp\Filters;

use Carbon\Carbon;
use Code16\Sharp\EntityList\EntityListDateRangeRequiredFilter;

class PassengerBirthdateFilter implements EntityListDateRangeRequiredFilter
{
    public function label(): string
    {
        return 'Born between';
    }

    public function defaultValue(): array
    {
        return [
            'start' => (new Carbon())->setDate(2014, 1, 1),
            'end'   => (new Carbon())->setDate(2014, 12, 31),
        ];
    }

    public function dateFormat(): string
    {
        return 'YYYY-MM-DD';
    }

    public function isMondayFirst(): bool
    {
        return false;
    }

    public function retainValueInSession(): bool
    {
        return true;
    }
}
