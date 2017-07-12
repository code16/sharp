<?php

namespace App\Sharp\Auth;

use Code16\Sharp\Auth\SharpAuthCheck as SharpAuthCheckContract;

class SharpAuthCheck implements SharpAuthCheckContract
{

    public function allowUserInSharp($user): bool
    {
        return $user->group == "admin";
    }
}