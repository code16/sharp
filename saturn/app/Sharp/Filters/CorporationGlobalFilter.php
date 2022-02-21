<?php

namespace App\Sharp\Filters;

use App\Corporation;
use Code16\Sharp\Utils\Filters\GlobalRequiredFilter;

class CorporationGlobalFilter extends GlobalRequiredFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureSearchable();
    }

    public function values(): array
    {
        return Corporation::orderBy('name')
            ->pluck('name', 'id')
            ->all();
    }

    public function defaultValue(): mixed
    {
        return Corporation::first()->id;
    }
}
