<?php

namespace App\Sharp\Posts;

use Code16\Sharp\Auth\SharpEntityPolicy;

class PostPolicy extends SharpEntityPolicy
{
    public function entity($user): bool
    {
        return true;
    }
}
