<?php

namespace App\Sharp\Filters;

use App\Pilot;
use Code16\Sharp\EntityList\EntityListSelectMultipleFilter;

class SpaceshipPilotsFilter implements EntityListSelectMultipleFilter
{
    /**
     * @return array
     */
    public function values()
    {
        return Pilot::orderBy("name")
            ->pluck("name", "id")
            ->all();
    }

    public function retainValueInSession()
    {
        return true;
    }
}