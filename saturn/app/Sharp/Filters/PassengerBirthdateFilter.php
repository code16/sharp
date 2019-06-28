<?php

namespace App\Sharp\Filters;

use Carbon\Carbon;
use Code16\Sharp\EntityList\EntityListDateRangeRequiredFilter;

class PassengerBirthdateFilter implements EntityListDateRangeRequiredFilter
{

    public function label()
    {
        return "Born between";
    }

    /**
     * @return array
     *
     * awaited format:
     *
     *    [
     *       "start" => Carbon::yesterday(),
     *       "end" => Carbon::today(),
     *    ]
     *
     * @throws \Exception
     */
    public function defaultValue()
    {
        return [
            "start" => (new Carbon())->setDate(2014,1,1),
            "end" => (new Carbon())->setDate(2014,12,31),
        ];
    }
}