<?php

namespace App\Sharp\States;

use App\Pilot;
use Code16\Sharp\EntityList\Commands\EntityState;

class PilotEntityState extends EntityState
{

    protected function buildStates()
    {
        $this->addState("active", "Active", "green")
            ->addState("inactive", "Retired", "orange");
    }

    public function updateState($instanceId, $stateId)
    {
        Pilot::findOrFail($instanceId)->update([
            "state" => $stateId
        ]);

        return $this->refresh($instanceId);
    }
}