<?php

namespace App\Sharp\States;

use App\User;
use Code16\Sharp\EntityList\Commands\SingleEntityState;

class AccountStatusState extends SingleEntityState
{
    protected function buildStates(): void
    {
        $this->addState('online', 'Online', 'green')
            ->addState('offline', 'Offline', 'red')
            ->addState('busy', 'Busy', 'orange');
    }

    protected function updateSingleState(string $stateId): array
    {
        User::findOrFail(auth()->id())->update([
            'status' => $stateId,
        ]);

        return $this->reload();
    }
}
