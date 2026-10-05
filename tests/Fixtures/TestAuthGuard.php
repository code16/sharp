<?php

namespace Code16\Sharp\Tests\Fixtures;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;

class TestAuthGuard implements Guard, StatefulGuard
{
    private ?Authenticatable $user = null;
    public bool $isOnce = false;

    public function check()
    {
        return $this->user !== null;
    }

    public function guest()
    {
        return $this->user === null;
    }

    public function user()
    {
        return $this->user;
    }

    public function id()
    {
        return $this->user?->id;
    }

    public function validate(array $credentials = []) {}

    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
    }

    public function hasUser()
    {
        return $this->user !== null;
    }

    public function attempt(array $credentials = [], $remember = false)
    {
        if ($credentials['email'] === 'test@example.org' && $credentials['password'] === 'password') {
            $this->login(new User(['id' => 1, ...$credentials, 'shouldRemember' => $remember]));

            return true;
        }

        return false;
    }

    public function once(array $credentials = [])
    {
        if ($credentials['email'] === 'test@example.org' && $credentials['password'] === 'password') {
            $this->setUser(new User(['id' => 1, ...$credentials]));
            $this->isOnce = true;

            return true;
        }

        return false;
    }

    public function login(Authenticatable $user, $remember = false)
    {
        $this->setUser($user);
    }

    public function loginUsingId($id, $remember = false)
    {
        if ($id === 1) {
            $this->login($user = new User(['id' => 1, 'email' => 'test@example.org']));

            return $user;
        }

        return false;
    }

    public function onceUsingId($id)
    {
        return $this->loginUsingId($id);
    }

    public function viaRemember() {}

    public function logout()
    {
        $this->user = null;
    }
}
