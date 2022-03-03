<?php

namespace App\Sharp\MyProfile;

use App\Models\User;

class MyProfilePolicy
{
    public function entity(User $user)
    {
        return true;
    }
}
