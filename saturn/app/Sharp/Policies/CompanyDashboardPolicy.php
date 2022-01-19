<?php

namespace App\Sharp\Policies;

use Code16\Sharp\Auth\SharpEntityPolicy;

class CompanyDashboardPolicy extends SharpEntityPolicy
{
    public function entity($user): bool
    {
        return $user->hasGroup('boss');
    }
}
