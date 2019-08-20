<?php

namespace App\Sharp\Commands;

use App\Pilot;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\EntityListQueryParams;

class PilotUpdateXPCommand extends EntityCommand
{
    /**
     * @return string
     */
    public function label(): string
    {
        return "Update experience";
    }

    public function description(): string
    {
        return "Add one year to every senior pilot experience.";
    }

    /**
     * @param EntityListQueryParams $params
     * @param array $data
     * @return array
     */
    public function execute(EntityListQueryParams $params, array $data = []): array
    {
        Pilot::where("role", "sr")->increment("xp");

        return $this->reload();
    }
}