<?php

namespace App\Sharp\Policies;

use App\User;

class UserPolicy
{

    public function entity(User $user)
    {
        return $user->hasGroup("boss");
    }
}
