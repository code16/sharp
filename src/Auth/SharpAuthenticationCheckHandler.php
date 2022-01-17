<?php

namespace Code16\Sharp\Auth;

use Illuminate\Contracts\Auth\Authenticatable;

interface SharpAuthenticationCheckHandler
{
    /**
     * @param Authenticatable $user
     *
     * @return bool
     */
    public function check(Authenticatable $user): bool;
}
