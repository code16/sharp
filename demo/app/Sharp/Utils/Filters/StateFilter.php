<?php

namespace App\Sharp\Utils\Filters;

use Code16\Sharp\Filters\SelectFilter;

class StateFilter extends SelectFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('State')
            ->configureRetainInSession();
    }

    public function values(): array
    {
        return [
            'online' => 'Online',
            'draft' => 'Draft',
        ];
    }
}
