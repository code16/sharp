<?php

namespace Code16\Sharp\Tests\Feature;

use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\StatefulGuard;

class LoginControllerTest extends BaseApiTest
{
    /** @test */
    public function we_can_login()
    {
        $this->post('/sharp/login', ['login' => 'test@example.org', 'password' => 'password'])
            ->assertRedirect('/sharp');

        $this->assertEquals('test@example.org', auth('sharp')->user()->email);
    }

    /** @test */
    public function we_can_logout()
    {
        $this->post('/sharp/login', ['login' => 'test@example.org', 'password' => 'password']);
        $this->post('/sharp/logout')->assertRedirect('/sharp/login');

        $this->assertNull(auth('sharp')->user());
    }

    /** @test */
    public function we_can_not_login_without_valid_payload()
    {
        $this->post('/sharp/login')
            ->assertSessionHasErrors(['login', 'password']);

        $this->post('/sharp/login', ['login' => 'bob@example.org'])
            ->assertSessionHasErrors(['password']);

        $this->post('/sharp/login', ['password' => 'password'])
            ->assertSessionHasErrors(['login']);
    }

    /** @test */
    public function we_can_check_remember_me_option()
    {
        $this->app['config']->set('sharp.auth.suggest_remember_me', true);

        $this
            ->post('/sharp/login', [
                'login' => 'test@example.org',
                'password' => 'password',
                'remember' => true,
            ])
            ->assertRedirect('/sharp');

        $this->assertTrue(auth('sharp')->user()->shouldRemember);
    }

    /** @test */
    public function we_can_not_check_remember_me_option_without_proper_config()
    {
        $this->app['config']->set('sharp.auth.suggest_remember_me', false);

        $this
            ->post('/sharp/login', [
                'login' => 'test@example.org',
                'password' => 'password',
                'remember' => true,
            ])
            ->assertRedirect('/sharp');

        $this->assertFalse(auth('sharp')->user()->shouldRemember);
    }

    protected function setUp(): void
    {
        parent::setUp();

        auth()->extend('sharp', function () {
            return new class implements Guard, StatefulGuard
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
        });

        $this->app['config']->set('sharp.auth.guard', 'sharp');

        $this->app['config']->set(
            'auth.guards.sharp', [
                'driver' => 'sharp',
                'provider' => 'users',
            ],
        );
    }
}
