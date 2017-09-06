<?php

namespace App\Sharp\Filters;

use App\Travel;
use Code16\Sharp\EntityList\EntityListFilter;

class PassengerTravelFilter implements EntityListFilter
{

    /**
     * @return array
     */
    public function values()
    {
        return Travel::orderBy("destination")
            ->pluck("destination", "id")
            ->all();
    }

    public function label()
    {
        return "Flies on";
    }
}