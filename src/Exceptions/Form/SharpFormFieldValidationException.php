<?php

namespace Code16\Sharp\Exceptions\Form;

use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Support\MessageBag;

class SharpFormFieldValidationException extends SharpException
{

    /**
     * @param MessageBag $validationErrors
     */
    function __construct(MessageBag $validationErrors)
    {
        parent::__construct("Invalid form field attributes : " . $validationErrors->toJson());
    }
}