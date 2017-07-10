<?php

namespace App\Sharp\States;

use App\Spaceship;
use Code16\Sharp\EntityList\EntityState;

class SpaceshipEntityState extends EntityState
{

    protected function buildStates()
    {
        $this->addState("active", "Active", "green");
        $this->addState("inactive", "Retired", "orange");
        $this->addState("building", "Building process", static::GRAY_COLOR);
        $this->addState("conception", "Conception phase", static::DARKGRAY_COLOR);
    }

    public function updateState($instanceId, $stateId)
    {
        Spaceship::findOrFail($instanceId)->update([
            "state" => $stateId
        ]);

//        return $this->reload();
//        return $this->refresh()->withData([]);
    }
}