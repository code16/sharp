<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class AuthorizationsTest extends BaseApiTest
{
    /** @test */
    public function unauthenticated_user_wont_pass_on_an_api_call()
    {
        $this->buildTheWorld(false, false);

        $this->json('get', '/sharp/api/list/person')->assertStatus(401);
    }

    /** @test */
    public function unauthenticated_user_are_redirected_on_a_web_call()
    {
        $this->buildTheWorld(false, false);

        $this->get('/sharp/list/person')->assertStatus(302);
    }

    /** @test */
    public function we_can_configure_a_custom_auth_guard()
    {
        $this->buildTheWorld();

        $authGuard = new AuthorizationsTestGuard(true);

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

        $authGuard->setInvalid();

        $this->get('/sharp/list/person')->assertStatus(302);
    }

    /** @test */
    public function we_can_configure_global_authorizations()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => false,
                "create" => false,
                "update" => false,
                "view" => false,
            ]
        );

        $this->json('post', '/sharp/api/form/person/50', [])->assertStatus(403);
        $this->json('get', '/sharp/api/list/person')->assertStatus(403);
        $this->json('get', '/sharp/api/form/person')->assertStatus(403);
        $this->json('post', '/sharp/api/form/person', [])->assertStatus(403);
        $this->json('delete', '/sharp/api/form/person/50')->assertStatus(403);
    }

    /** @test */
    public function default_global_authorizations_is_handled()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => false,
                "update" => false,
            ]
        );

        $this->json('get', '/sharp/api/list/person')->assertStatus(200);
        $this->json('get', '/sharp/api/form/person')->assertStatus(200);
        $this->json('post', '/sharp/api/form/person', [])->assertStatus(200);
        $this->json('post', '/sharp/api/form/person/50', [])->assertStatus(403);
        $this->json('delete', '/sharp/api/form/person/50')->assertStatus(403);
    }

    /** @test */
    public function authorizations_are_appended_to_the_response()
    {
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.entities.person.authorizations', [
                "delete" => false,
                "update" => false,
            ]
        );

        $this->json('get', '/sharp/api/list/person')->assertJson([
            "authorizations" => [
                "delete" => false,
                "update" => false,
                "create" => true,
                "view" => true,
            ]
        ]);
    }

}

class AuthorizationsTestGuard implements \Illuminate\Contracts\Auth\Guard
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