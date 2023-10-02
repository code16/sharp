<?php

namespace Code16\Sharp\Exceptions\Form;

use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SharpApplicativeException extends SharpException
{
    public function getStatusCode(): int
    {
        return 417;
    }
}
