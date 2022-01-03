<?php

namespace App\Sharp\Filters;

use App\Travel;
use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;

class PassengerTravelFilter extends EntityListSelectFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel("Flies on")
            ->configureSearchable()
            ->configureSearchKeys(["label", "continent"])
            ->configureTemplate("{{label}}<br><small>{{continent}}</small>");
    }

    public function values(): array
    {
        return Travel::orderBy("destination")
            ->get()
            ->map(function(Travel $travel) {
                return [
                    "id" => $travel->id,
                    "label" => $travel->destination,
                    "continent" => ["Europe", "America", "Oceania", "Asia", "Africa"][rand(0, 4)]
                ];
            })
            ->all();
    }
}