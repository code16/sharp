<?php

namespace Code16\Sharp\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

interface SharpAuthenticationCheckHandler
{
    public function check(Authenticatable $user): bool;
}
