<?php

namespace Code16\Sharp\Auth;

use Illuminate\Auth\Access\Gate;
use Illuminate\Contracts\Container\Container;

/**
 * We have to define our own Gate to ensure that the correct
 * user (which could be from a custom Guard) is returned.
 *
 * Class SharpGate
 * @package Code16\Sharp\Auth
 */
class SharpGate extends Gate
{
    function __construct(Container $container)
    {
        parent::__construct($container, function () {
            return sharp_user();
        });
    }
}