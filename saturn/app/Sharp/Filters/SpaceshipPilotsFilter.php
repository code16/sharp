<?php

namespace App\Sharp\Filters;

use App\Pilot;
use Code16\Sharp\EntitiesList\EntitiesListFilter;

class SpaceshipPilotsFilter extends EntitiesListFilter
{
    protected $multiple = true;

    public function values()
    {
        return Pilot::orderBy("name")
            ->pluck("name", "id");
    }
}