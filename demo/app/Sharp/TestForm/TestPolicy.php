<?php

namespace App\Sharp\TestForm;

class TestPolicy
{
    public function entity($user)
    {
        return $user->isAdmin();
    }
}
