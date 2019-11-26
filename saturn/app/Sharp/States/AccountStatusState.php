<?php

namespace App\Sharp\States;

use App\User;
use Code16\Sharp\EntityList\Commands\SingleEntityState;

class AccountStatusState extends SingleEntityState
{

    /**
     * @return mixed
     */
    protected function buildStates()
    {
        $this->addState("online", "Online", "green")
            ->addState("offline", "Offline", "red")
            ->addState("busy", "Busy", "orange");
    }

    /**
     * @param string $stateId
     * @return mixed
     */
    protected function updateSingleState(string $stateId)
    {
        User::findOrFail(auth()->id)->update([
            "status" => $stateId
        ]);

        return $stateId;
    }
}