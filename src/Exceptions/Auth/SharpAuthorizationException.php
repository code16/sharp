<?php

namespace Code16\Sharp\Exceptions\Auth;

use Code16\Sharp\Exceptions\SharpException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class SharpAuthorizationException extends SharpException implements HttpExceptionInterface
{
    public function getStatusCode(): int
    {
        return 403;
    }
}
