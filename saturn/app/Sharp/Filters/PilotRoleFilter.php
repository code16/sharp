<?php

namespace App\Sharp\Filters;

use Code16\Sharp\EntityList\EntityListSelectFilter;

class PilotRoleFilter implements EntityListSelectFilter
{
    public function values()
    {
        return ["jr" => "Junior", "sr" => "Senior"];
    }

    public function label()
    {
        return "Role";
    }
}