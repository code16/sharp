<?php

namespace App\Sharp\Policies;

use App\User;

/**
 * This policy is written as a legacy from Sharp 6.
 * It will be ultimately decorate in a SharpEntityPolicyLegacyDecorator object.
 */
class UserPolicy
{
    public function entity(User $user)
    {
        return $user->hasGroup('boss');
    }
}
