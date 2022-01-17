<?php

namespace Code16\Sharp\Exceptions\Show;

use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Support\MessageBag;

class SharpShowFieldValidationException extends SharpException
{
    public function __construct(MessageBag $validationErrors)
    {
        parent::__construct('Invalid show label attributes : '.$validationErrors->toJson());
    }
}
