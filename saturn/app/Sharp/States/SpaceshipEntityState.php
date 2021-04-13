<?php

namespace App\Sharp\States;

use App\Spaceship;
use Code16\Sharp\EntityList\Commands\EntityState;

class SpaceshipEntityState extends EntityState
{
    protected function buildStates(): void
    {
        $this->addState("active", "Active", "green")
            ->addState("inactive", "Retired", "orange")
            ->addState("building", "Building process", "#8C9BA5")
            ->addState("conception", "Conception phase", "#394b54");
    }

    public function updateState($instanceId, $stateId): array
    {
        Spaceship::findOrFail($instanceId)->update([
            "state" => $stateId
        ]);

        return $this->refresh($instanceId);
    }

    public function authorizeFor($instanceId): bool {
        return $instanceId % 2 == 0;
    }
}
