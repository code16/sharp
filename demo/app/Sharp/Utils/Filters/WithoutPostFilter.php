<?php

namespace App\Sharp\Utils\Filters;

use Code16\Sharp\EntityList\Filters\EntityListCheckFilter;

class WithoutPostFilter extends EntityListCheckFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Without post only');
    }
}
