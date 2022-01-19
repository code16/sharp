<?php

namespace App\Sharp\Auth;

use Code16\Sharp\Auth\SharpAuthenticationCheckHandler;

class SharpCheckHandler implements SharpAuthenticationCheckHandler
{
    public function check($user): bool
    {
        return $user->hasGroup('sharp');
    }
}
