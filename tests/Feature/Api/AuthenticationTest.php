<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Auth\SharpAuthCheck;
use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class AuthenticationTest extends BaseApiTest
{
    /** @test */
    public function unauthenticated_user_wont_pass_on_an_api_call()
    {
        $this->buildTheWorld();

        $this->json('get', '/sharp/api/list/person')->assertStatus(401);
    }

    /** @test */
    public function unauthenticated_user_are_redirected_on_a_web_call()
    {
        $this->buildTheWorld();

        $this->get('/sharp/list/person')->assertStatus(302);
    }

    /** @test */
    public function we_can_configure_a_custom_auth_guard()
    {
        $this->buildTheWorld();
        $this->login();

        $authGuard = new AuthenticationTestGuard(true);

        Auth::extend('sharp', function() use($authGuard) {
            return $authGuard;
        });

        $this->app['config']->set(
            'sharp.auth.guard',
            'sharp'
        );

        $this->app['config']->set([
            'auth.guards.sharp' => [
                'driver' => 'sharp',
                'provider' => 'users',
            ]
        ]);

        $this->get('/sharp/list/person')->assertStatus(200);
        $this->json('get', '/sharp/api/list/person')->assertStatus(200);

        $authGuard->setInvalid();

        $this->get('/sharp/list/person')->assertStatus(302);
        $this->json('get', '/sharp/api/list/person')->assertStatus(401);
    }

    /** @test */
    public function we_can_configure_an_additional_auth_check()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.auth.check',
            AuthenticationTestCheck::class
        );

        $this->actingAs(new User(["name" => "Bob"]));
        $this->get('/sharp/list/person')->assertStatus(302);
        $this->json('get', '/sharp/api/list/person')->assertStatus(401);

        $this->actingAs(new User(["name" => "John"]));
        $this->get('/sharp/list/person')->assertStatus(200);
        $this->json('get', '/sharp/api/list/person')->assertStatus(200);
    }

}

class AuthenticationTestGuard implements \Illuminate\Contracts\Auth\Guard
{
    private $isValid;

    public function __construct(bool $isValid)
    {
        $this->isValid = $isValid;
    }
    public function check()
    {
       return $this->isValid;
    }
    public function guest()
    {
        return !$this->isValid;
    }
    public function user()
    {
        return $this->isValid ? new User() : null;
    }
    public function id()
    {
        return $this->isValid ? 1 : null;
    }
    public function validate(array $credentials = [])
    {
        return true;
    }
    public function setUser(Authenticatable $user) {}

    public function setInvalid()
    {
        $this->isValid = false;
    }
}

class AuthenticationTestCheck implements SharpAuthCheck
{
    function allowUserInSharp($user): bool
    {
        return $user->name == "John";
    }
}