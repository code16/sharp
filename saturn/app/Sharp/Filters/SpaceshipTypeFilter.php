<?php

namespace App\Sharp\Filters;

use App\SpaceshipType;
use Code16\Sharp\EntitiesList\EntitiesListFilter;

class SpaceshipTypeFilter extends EntitiesListFilter
{

    public function values()
    {
        return SpaceshipType::orderBy("label")
            ->pluck("label", "id");
    }
}