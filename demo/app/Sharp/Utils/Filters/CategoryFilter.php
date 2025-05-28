<?php

namespace App\Sharp\Utils\Filters;

use App\Models\Category;
use Code16\Sharp\Filters\SelectMultipleFilter;

class CategoryFilter extends SelectMultipleFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Categories')
            ->configureSearchable();
    }

    public function values(): array
    {
        return Category::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}
