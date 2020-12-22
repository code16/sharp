<?php

namespace App\Sharp\Filters;

use Code16\Sharp\EntityList\EntityListSelectFilter;

class PilotRoleFilter implements EntityListSelectFilter
{
    public function label(): string
    {
        return "Role";
    }

    public function values(): array
    {
        return ["jr" => "Junior", "sr" => "Senior"];
    }
}