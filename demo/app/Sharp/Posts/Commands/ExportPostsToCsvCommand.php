<?php

namespace App\Sharp\Posts\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;

class ExportPostsToCsvCommand extends EntityCommand
{
    public function label(): string
    {
        return "Download posts as a CSV file";
    }

    public function buildCommandConfig(): void
    {
        $this->configureDescription("Filters will be taken into account ");
    }

    public function execute(array $data = []): array
    {
        return $this->download("fake.csv", "fake.csv", "local");
    }
}
