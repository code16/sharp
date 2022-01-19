<?php

namespace App\Sharp\Filters;

use App\Spaceship;
use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;

class PilotSpaceshipFilter extends EntityListSelectFilter
{
    public function buildFilterConfig(): void
    {
        $this
            ->configureKey('pilot')
            ->configureLabel('Spaceship');
    }

    public function values(): array
    {
        return Spaceship::orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }
}
