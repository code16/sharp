<?php

namespace App\Sharp\Filters;

use App\Pilot;
use Code16\Sharp\EntityList\EntityListSelectMultipleFilter;

class SpaceshipPilotsFilter implements EntityListSelectMultipleFilter
{
    public function values(): array
    {
        return Pilot::orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function retainValueInSession(): bool
    {
        return true;
    }
}
