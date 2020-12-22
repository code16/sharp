<?php

namespace Code16\Sharp\Exceptions\Dashboard;

use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Contracts\Support\MessageBag;

class SharpWidgetValidationException extends SharpException
{
    function __construct(MessageBag $validationErrors)
    {
        parent::__construct("Invalid widget attributes : " . $validationErrors->toJson());
    }
}