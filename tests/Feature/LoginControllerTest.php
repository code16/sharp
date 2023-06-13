<?php

namespace Code16\Sharp\Tests\Feature;

use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\TestAuthGuard;

class LoginControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        auth()->extend('sharp', fn () => new TestAuthGuard());
        $this->app['config']->set('sharp.auth.guard', 'sharp');
        $this->app['config']->set(
            'auth.guards.sharp', [
                'driver' => 'sharp',
                'provider' => 'users',
            ],
        );
    }

    /** @test */
    public function we_can_login()
    {
        $this
            ->from(route('code16.sharp.login'))
            ->post(route('code16.sharp.login.post'), ['login' => 'test@example.org', 'password' => 'password'])
            ->assertRedirect(route('code16.sharp.home'));

        $this->assertEquals('test@example.org', auth('sharp')->user()->email);
    }

    /** @test */
    public function we_can_logout()
    {
        $this->post(route('code16.sharp.login.post'), ['login' => 'test@example.org', 'password' => 'password']);
        $this->post(route('code16.sharp.logout'))->assertRedirect(route('code16.sharp.login'));

        $this->assertNull(auth('sharp')->user());
    }

    /** @test */
    public function we_can_not_login_without_valid_payload()
    {
        $this->post(route('code16.sharp.login.post'))
            ->assertSessionHasErrors(['login', 'password']);

        $this->post(route('code16.sharp.login.post'), ['login' => 'bob@example.org'])
            ->assertSessionHasErrors(['password']);

        $this->post(route('code16.sharp.login.post'), ['password' => 'password'])
            ->assertSessionHasErrors(['login']);
    }

    /** @test */
    public function we_can_check_remember_me_option()
    {
        $this->app['config']->set('sharp.auth.suggest_remember_me', true);

        $this
            ->post(route('code16.sharp.login.post'), [
                'login' => 'test@example.org',
                'password' => 'password',
                'remember' => true,
            ])
            ->assertRedirect(route('code16.sharp.home'));

        $this->assertTrue(auth('sharp')->user()->shouldRemember);
    }

    /** @test */
    public function we_can_not_check_remember_me_option_without_proper_config()
    {
        $this->app['config']->set('sharp.auth.suggest_remember_me', false);

        $this
            ->post(route('code16.sharp.login.post'), [
                'login' => 'test@example.org',
                'password' => 'password',
                'remember' => true,
            ])
            ->assertRedirect(route('code16.sharp.home'));

        $this->assertFalse(auth('sharp')->user()->shouldRemember);
    }

    /** @test */
    public function we_hit_rate_limiter_if_configured()
    {
        config(['sharp.auth.rate_limiting' => ['enabled' => true, 'max_attempts' => 1]]);

        $this->post(route('code16.sharp.login.post'), ['login' => 'test@example.org', 'password' => 'bad'])
            ->assertSessionHasErrors(['login' => trans('sharp::auth.invalid_credentials')]);

        $this->post(route('code16.sharp.login.post'), ['login' => 'test@example.org', 'password' => 'too-many'])
            ->assertSessionHasErrors('login');

        $this->assertStringStartsWith('Too many login attempts', session()->get('errors')->first('login'));

        $this->assertNull(auth('sharp')->user());
    }
}
