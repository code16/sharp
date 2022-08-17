<?php

namespace App\Sharp\Utils\Filters;

use App\Models\Category;
use Code16\Sharp\EntityList\Filters\EntityListSelectMultipleFilter;

class CategoryFilter extends EntityListSelectMultipleFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Categories');
    }

    public function values(): array
    {
        return Category::orderBy('name')
            ->pluck('name', 'id')
            ->toArray();
    }
}
