<?php

namespace Code16\Sharp\Tests\Fixtures;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;

class TestAuthGuard implements Guard, StatefulGuard
{
    private ?User $user = null;

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
        return $this->hasUser() ? 1 : null;
    }

    public function validate(array $credentials = [])
    {
    }

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
            $this->login(new User(array_merge($credentials, ['shouldRemember' => $remember])));

            return true;
        }

        return false;
    }

    public function once(array $credentials = [])
    {
        if ($credentials['email'] === 'test@example.org' && $credentials['password'] === 'password') {
            $this->setUser(new User($credentials));

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
    }

    public function onceUsingId($id)
    {
    }

    public function viaRemember()
    {
    }

    public function logout()
    {
        $this->user = null;
    }
};