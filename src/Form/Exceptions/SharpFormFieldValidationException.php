<?php

namespace Code16\Sharp\Form\Exceptions;

use Illuminate\Support\MessageBag;

class SharpFormFieldValidationException extends \Exception
{

    /**
     * @param MessageBag $validationErrors
     */
    function __construct(MessageBag $validationErrors)
    {
        parent::__construct("Invalid form field attributes : " . $validationErrors->toJson());
    }
}