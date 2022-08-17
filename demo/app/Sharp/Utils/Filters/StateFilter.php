<?php

namespace App\Sharp\Utils\Filters;

use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;

class StateFilter extends EntityListSelectFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('State');
    }

    public function values(): array
    {
        return [
            'online' => 'Online',
            'draft' => 'Draft',
        ];
    }
}
