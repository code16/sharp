<?php

namespace Code16\Sharp\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SharpInvalidEntityKeyException extends SharpException
{
    public function render(Request $request): Response
    {
        abort(404, $this->message);
    }
}
