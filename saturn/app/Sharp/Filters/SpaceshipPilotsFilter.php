<?php

namespace App\Sharp\Filters;

use App\Pilot;
use Code16\Sharp\EntityList\EntityListMultipleFilter;

class SpaceshipPilotsFilter implements EntityListMultipleFilter
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