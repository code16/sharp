<?php

namespace Code16\Sharp\Tests\Fixtures;

use Closure;
use Illuminate\Contracts\Auth\PasswordBroker as PasswordBrokerContract;

class TestPasswordBroker implements PasswordBrokerContract
{
    public function sendResetLink(array $credentials, ?Closure $callback = null)
    {
        return $credentials['email'] === 'test@example.org'
            ? static::RESET_LINK_SENT
            : static::RESET_THROTTLED;
    }

    public function reset(array $credentials, Closure $callback)
    {
        $callback(
            new User(['email' => $credentials['email']]),
            $credentials['password']
        );

        return static::PASSWORD_RESET;
    }
}
