<?php

namespace App\Sharp\Commands;

use App\Pilot;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\EntityListQueryParams;

class PilotUpdateXPCommand extends EntityCommand
{
    public function label(): string
    {
        return "Update experience";
    }
    
    public function buildCommandConfig(): void
    {
        $this->configureDescription("Add one year to every senior pilot experience.");
    }

    public function execute(array $data = []): array
    {
        Pilot::where("role", "sr")->increment("xp");

        return $this->reload();
    }
}