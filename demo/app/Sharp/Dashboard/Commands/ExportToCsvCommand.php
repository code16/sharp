<?php

namespace App\Sharp\Dashboard\Commands;

use Code16\Sharp\Dashboard\Commands\DashboardCommand;

class ExportToCsvCommand extends DashboardCommand
{
    public function buildCommandConfig(): void
    {
        $this->configureDescription("Date range will be taken into account");
    }

    public function label(): string
    {
        return "Download stats as a CSV file";
    }

    public function execute(array $data = []): array
    {
        return $this->download("fake.csv", "fake.csv", "local");
    }
}
