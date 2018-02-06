<?php

namespace App\Sharp\Filters;

use App\Travel;
use Code16\Sharp\EntityList\EntityListFilter;

class PassengerTravelFilter implements EntityListFilter
{

    /**
     * @return array
     */
    public function values()
    {
        return Travel::orderBy("destination")
            ->get()
            ->map(function(Travel $travel) {
                return [
                    "id" => $travel->id,
                    "country" => $travel->destination,
                    "continent" => ["Europe", "America", "Oceania", "Asia", "Africa"][rand(0, 4)]
                ];
            })
            ->all();
    }

    public function label()
    {
        return "Flies on";
    }

    public function isSearchable(): bool
    {
        return true;
    }

    public function searchKeys(): array
    {
        return ["country", "continent"];
    }

    public function template(): string
    {
        return "{{country}}<br><small>{{continent}}</small>";
    }
}