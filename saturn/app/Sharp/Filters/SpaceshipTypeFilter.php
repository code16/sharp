<?php

namespace App\Sharp\Filters;

use App\SpaceshipType;
use Code16\Sharp\EntityList\EntityListFilter;

class SpaceshipTypeFilter implements EntityListFilter
{

    public function values()
    {
        return SpaceshipType::orderBy("label")
            ->pluck("label", "id");
    }

    /**
     * @return bool
     */
    public function multiple()
    {
        return false;
    }
}