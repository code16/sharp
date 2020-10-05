<?php

namespace App\Sharp\Filters;

use App\SpaceshipType;
use Code16\Sharp\EntityList\EntityListSelectRequiredFilter;

class SpaceshipTypeFilter implements EntityListSelectRequiredFilter
{
    public function values()
    {
        return SpaceshipType::orderBy("label")
            ->pluck("label", "id")
            ->all();
    }

    public function defaultValue()
    {
        return SpaceshipType::orderBy("label")->first()->id;
    }

    public function label()
    {
        return "Ship type";
    }

    public function retainValueInSession()
    {
        return true;
    }
}