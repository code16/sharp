<?php

namespace App\Sharp\Filters;

use App\Corporation;
use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;

class CorporationGlobalFilter implements GlobalRequiredFilter
{
    public function values(): array
    {
        return Corporation::orderBy("name")
            ->pluck("name", "id")
            ->all();
    }

    public function defaultValue()
    {
        return Corporation::first()->id;
    }

    public function isSearchable(): bool
    {
        return true;
    }
}