<?php

namespace App\Sharp\Filters\EntityList;

use Code16\Sharp\Filters\CheckFilter;

class TestCheckFilter extends CheckFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel('Check');
    }
}
