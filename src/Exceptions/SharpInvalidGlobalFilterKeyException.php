<?php

namespace Code16\Sharp\Exceptions;

class SharpInvalidGlobalFilterKeyException extends SharpException
{
    public function getStatusCode(): int
    {
        return 404;
    }
}
