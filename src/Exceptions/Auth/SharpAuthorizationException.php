<?php

namespace Code16\Sharp\Exceptions\Auth;

use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SharpAuthorizationException extends SharpException
{
    public function render(Request $request): Response
    {
        abort(403, $this->message);
    }
}
