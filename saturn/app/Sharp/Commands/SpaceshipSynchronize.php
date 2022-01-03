<?php

namespace App\Sharp\Commands;

use App\Sharp\Filters\SpaceshipTypeFilter;
use App\SpaceshipType;
use Code16\Sharp\EntityList\Commands\EntityCommand;

class SpaceshipSynchronize extends EntityCommand
{
    public function label(): string
    {
        return sprintf(
            "Synchronize the gamma-spectrum of %s spaceships",
            SpaceshipType::findOrFail($this->queryParams->filterFor(SpaceshipTypeFilter::class))->label
        );
    }
    
    public function buildCommandConfig(): void
    {
        $this
            ->configureDescription("Let's be honest: this command is a fraud. It's just an empty command for test purpose.")
            ->configureConfirmationText("Sure, really?");
    }

    public function execute(array $data=[]): array
    {
        sleep(1);

        return $this->info(
            sprintf(
                "Gamma spectrum of %s spaceships synchronized!",
                SpaceshipType::findOrFail($this->queryParams->filterFor(SpaceshipTypeFilter::class))->label
            )
        );
    }

    public function authorize():bool
    {
        return sharp_user()->hasGroup("boss");
    }
}