<?php

namespace App\Sharp\Filters;

use App\Spaceship;
use Code16\Sharp\Dashboard\DashboardSelectMultipleFilter;

class TravelsDashboardSpaceshipsFilter implements DashboardSelectMultipleFilter
{
    public function values(): array
    {
        return Spaceship::orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function isSearchable(): bool
    {
        return true;
    }
}
