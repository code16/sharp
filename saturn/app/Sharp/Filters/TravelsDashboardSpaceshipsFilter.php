<?php

namespace App\Sharp\Filters;

use App\Spaceship;
use Code16\Sharp\Dashboard\DashboardMultipleFilter;

class TravelsDashboardSpaceshipsFilter implements DashboardMultipleFilter
{

    public function values()
    {
        return Spaceship::orderBy("name")
            ->pluck("name", "id")
            ->all();
    }

    public function isSearchable(): bool
    {
        return true;
    }

}