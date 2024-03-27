<?php

namespace Code16\Sharp\Exceptions\Form;

use Code16\Sharp\Exceptions\SharpException;

class SharpApplicativeException extends SharpException
{
    public function getStatusCode(): int
    {
        return 417;
    }
}
