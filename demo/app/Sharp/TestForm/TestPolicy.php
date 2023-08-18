<?php

namespace App\Sharp\TestForm;

use Code16\Sharp\Auth\SharpEntityPolicy;

class TestPolicy extends SharpEntityPolicy
{
    public function entity($user): bool
    {
        return $user->isAdmin();
    }
}
