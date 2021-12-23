<?php

namespace App\Sharp\Policies;

use App\User;

class CompanyDashboardLegacyPolicy
{
    public function view(User $user)
    {
        return $user->hasGroup("boss");
    }
}
