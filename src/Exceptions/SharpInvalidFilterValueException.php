<?php

namespace Code16\Sharp\Exceptions;

use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class SharpInvalidFilterValueException extends SharpException implements HttpExceptionInterface
{
    public function getStatusCode(): int
    {
        return 404;
    }
}
