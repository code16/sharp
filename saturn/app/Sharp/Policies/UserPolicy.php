<?php

namespace App\Sharp\Policies;

use Code16\Sharp\Auth\SharpEntityPolicy;

class UserPolicy extends SharpEntityPolicy
{

    public function entity($user): bool
    {
        return $user->hasGroup("boss");
    }
}
