<?php

namespace App\Sharp\Filters;

use Code16\Sharp\EntityList\EntityListDateRangeFilter;

class PassengerBirthdateFilter implements EntityListDateRangeFilter
{

    /**
     * @return array
     */
    public function values()
    {
        return [];
    }

    public function label()
    {
        return "Born between";
    }
}