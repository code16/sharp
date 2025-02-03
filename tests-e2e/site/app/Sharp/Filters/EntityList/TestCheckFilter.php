<?php

namespace App\Sharp\Filters\EntityList;

use Code16\Sharp\EntityList\Filters\EntityListCheckFilter;

class TestCheckFilter extends EntityListCheckFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Check');
    }
}
