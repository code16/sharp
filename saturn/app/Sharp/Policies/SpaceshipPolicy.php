<?php

namespace App\Sharp\Policies;

use Code16\Sharp\Auth\SharpEntityPolicy;

class SpaceshipPolicy extends SharpEntityPolicy
{

    public function view($user, $instanceId): bool
    {
        return $instanceId%2 == 0 || $instanceId > 10;
    }

    public function update($user, $instanceId): bool
    {
        return $instanceId%2 == 0;
    }
}
