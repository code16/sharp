<?php

namespace App\Sharp\Filters;

use App\Pilot;
use Code16\Sharp\EntityList\Filters\EntityListSelectMultipleFilter;

class SpaceshipPilotsFilter extends EntityListSelectMultipleFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel("Pilots");
    }

    public function values(): array
    {
        return Pilot::orderBy("name")
            ->pluck("name", "id")
            ->all();
    }
}