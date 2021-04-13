<?php

namespace App\Sharp\Filters;

use App\Spaceship;
use Code16\Sharp\EntityList\EntityListSelectFilter;

class PilotSpaceshipFilter implements EntityListSelectFilter
{
    public function values(): array
    {
        return Spaceship::orderBy("name")
            ->pluck("name", "id")
            ->all();
    }
}