<?php

namespace Code16\Sharp\Exceptions\EntityList;

use Code16\Sharp\Exceptions\SharpException;

class SharpInvalidEntityStateException extends SharpException
{
    public function getStatusCode(): int
    {
        return 422;
    }
}
