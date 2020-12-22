<?php

namespace App\Sharp\Filters;

use App\SpaceshipType;
use Code16\Sharp\EntityList\EntityListSelectRequiredFilter;

class SpaceshipTypeFilter implements EntityListSelectRequiredFilter
{
    public function label(): string
    {
        return "Ship type";
    }

    public function values(): array
    {
        return SpaceshipType::orderBy("label")
            ->pluck("label", "id")
            ->all();
    }

    public function defaultValue()
    {
        return SpaceshipType::orderBy("label")->first()->id;
    }

    public function retainValueInSession(): bool
    {
        return true;
    }
}