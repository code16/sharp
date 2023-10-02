<?php

namespace Code16\Sharp\Exceptions\Auth;

use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SharpAuthorizationException extends SharpException
{
    public function getStatusCode(): int
    {
        return 403;
    }
}
