<?php

namespace App\Sharp\Filters;

use App\SpaceshipType;
use Code16\Sharp\EntityList\Filters\EntityListSelectRequiredFilter;

class SpaceshipTypeFilter extends EntityListSelectRequiredFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel("Ship type")
            ->configureKey("s-type")
            ->configureRetainInSession();
    }

    public function values(): array
    {
        return SpaceshipType::orderBy("label")
            ->pluck("label", "id")
            ->all();
    }

    public function defaultValue(): mixed
    {
        return SpaceshipType::orderBy("label")
            ->first()
            ->id;
    }
}