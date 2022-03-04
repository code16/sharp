<?php

namespace App\Sharp\Filters;

use App\Travel;
use Code16\Sharp\EntityList\EntityListSelectFilter;

class PassengerTravelFilter implements EntityListSelectFilter
{
    public function values(): array
    {
        return Travel::orderBy('destination')
            ->get()
            ->map(function (Travel $travel) {
                return [
                    'id'        => $travel->id,
                    'label'     => $travel->destination,
                    'continent' => ['Europe', 'America', 'Oceania', 'Asia', 'Africa'][rand(0, 4)],
                ];
            })
            ->all();
    }

    public function label(): string
    {
        return 'Flies on';
    }

    public function isSearchable(): bool
    {
        return true;
    }

    public function searchKeys(): array
    {
        return ['label', 'continent'];
    }

    public function template(): string
    {
        return '{{label}}<br><small>{{continent}}</small>';
    }
}
