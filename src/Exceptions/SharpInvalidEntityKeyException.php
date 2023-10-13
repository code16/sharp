<?php

namespace Code16\Sharp\Exceptions;

class SharpInvalidEntityKeyException extends SharpException
{
    public function getStatusCode(): int
    {
        return 404;
    }
}
