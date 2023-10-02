<?php

namespace Code16\Sharp\Exceptions;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SharpInvalidEntityKeyException extends SharpException
{
    public function getStatusCode(): int
    {
        return 404;
    }
}
