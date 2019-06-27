<?php

namespace App\Sharp\Filters;

use Code16\Sharp\EntityList\EntityListDateRangeFilter;

class PassengerBirthdateFilter implements EntityListDateRangeFilter
{

    public function label()
    {
        return "Born between";
    }
}