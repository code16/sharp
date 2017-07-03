<?php

namespace App\Sharp\Filters;

use App\Pilot;
use Code16\Sharp\EntitiesList\EntitiesListFilter;

class SpaceshipPilotsFilter implements EntitiesListFilter
{
    /**
     * @return array
     */
    public function values()
    {
        return Pilot::orderBy("name")
            ->pluck("name", "id");
    }

    /**
     * @return bool
     */
    public function multiple()
    {
        return true;
    }
}