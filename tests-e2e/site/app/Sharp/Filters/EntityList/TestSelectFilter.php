<?php

namespace App\Sharp\Filters\EntityList;

use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;

class TestSelectFilter extends EntityListSelectFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Select');
    }

    public function values(): array
    {
        return [
            1 => 'Option 1',
            2 => 'Option 2',
            3 => 'Option 3',
            4 => 'Option 4',
            5 => 'Option 5',
            6 => 'Option 6',
            7 => 'Option 7',
            8 => 'Option 8',
            9 => 'Option 9',
            10 => 'Option 10',
        ];
    }
}
