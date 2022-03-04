<?php

namespace App\Sharp\Policies;

use App\User;

class CompanyDashboardPolicy
{
    /**
     * @param User $user
     *
     * @return bool
     */
    public function view(User $user)
    {
        return $user->hasGroup('boss');
    }
}
