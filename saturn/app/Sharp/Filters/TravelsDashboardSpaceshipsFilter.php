<?php

namespace App\Sharp\Filters;

use App\Spaceship;
use Code16\Sharp\Dashboard\Filters\DashboardSelectMultipleFilter;

class TravelsDashboardSpaceshipsFilter extends DashboardSelectMultipleFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel("Travels")
            ->configureSearchable();
    }

    public function values(): array
    {
        return Spaceship::orderBy("name")
            ->pluck("name", "id")
            ->all();
    }
}