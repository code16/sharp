<?php

namespace App\Sharp\Filters;

use Code16\Sharp\EntityList\Filters\EntityListSelectFilter;

class PilotRoleFilter extends EntityListSelectFilter
{
    public function buildFilterConfig(): void
    {
        $this->configureLabel("Role");
    }

    public function values(): array
    {
        return ["jr" => "Junior", "sr" => "Senior"];
    }
}