<?php

namespace App\Sharp\States;

use App\Spaceship;
use Code16\Sharp\EntityList\Commands\EntityState;

class SpaceshipEntityState extends EntityState
{

    protected function buildStates()
    {
        $this->addState("active", "Active", "green")
            ->addState("inactive", "Retired", "orange")
            ->addState("building", "Building process", static::GRAY_COLOR)
            ->addState("conception", "Conception phase", static::DARKGRAY_COLOR);
    }

    public function updateState($instanceId, $stateId)
    {
        Spaceship::findOrFail($instanceId)->update([
            "state" => $stateId
        ]);

        return $this->refresh($instanceId);
    }
}