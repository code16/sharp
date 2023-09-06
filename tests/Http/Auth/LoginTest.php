<?php

use Code16\Sharp\Auth\SharpAuthenticationCheckHandler;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\TestAuthGuard;
use Code16\Sharp\Tests\Fixtures\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );
});

function setTestAuthGuard(): void
{
    auth()->extend('sharp', fn () => new TestAuthGuard());
    config()->set('sharp.auth.guard', 'sharp');
    config()->set('auth.guards.sharp', ['driver' => 'sharp', 'provider' => 'users']);
}

it('redirects guests to the login page', function () {
    $this->get('/sharp/s-list/person')
        ->assertRedirect('/sharp/login');
});

it('redirects users from the login page to the home page', function () {
    login();

    $this->get('/sharp/login')
        ->assertRedirect('/sharp');
});

it('displays the login page', function () {
    $this->get('/sharp/login')
        ->assertInertia(fn (Assert $page) => $page
            ->component('Auth/Login')
        );
});

it('allows guests to login', function () {
    setTestAuthGuard();

    $this
        ->post('/sharp/login', [
            'login' => 'test@example.org',
            'password' => 'password'
        ])
        ->assertRedirect('/sharp');

    expect(auth('sharp')->check())->toBeTrue();
});

it('does not allow to login with invalid payload', function () {
    $this->post('/sharp/login')
        ->assertSessionHasErrors(['login', 'password']);

    $this->post('/sharp/login', ['login' => 'bob@example.org'])
        ->assertSessionHasErrors(['password']);

    $this->post('/sharp/login', ['password' => 'password'])
        ->assertSessionHasErrors(['login']);
});

it('handles remember_me option', function () {
    setTestAuthGuard();

    config()->set('sharp.auth.suggest_remember_me', true);

    $this
        ->post('/sharp/login', [
            'login' => 'test@example.org',
            'password' => 'password',
            'remember' => true,
        ])
        ->assertRedirect('/sharp');

    expect(auth('sharp')->user()->shouldRemember)->toBeTrue();
});

it('does not allow remember_me option without proper config', function () {
    setTestAuthGuard();

    config()->set('sharp.auth.suggest_remember_me', false);

    $this
        ->post('/sharp/login', [
            'login' => 'test@example.org',
            'password' => 'password',
            'remember' => true,
        ])
        ->assertRedirect('/sharp');

    expect(auth('sharp')->user()->shouldRemember)->toBeFalse();
});

it('hits rate limiter if configured', function () {
    setTestAuthGuard();

    config()->set('sharp.auth.rate_limiting', ['enabled' => true, 'max_attempts' => 1]);

    $this->post('/sharp/login', ['login' => 'test@example.org', 'password' => 'bad'])
        ->assertSessionHasErrors(['login' => trans('sharp::auth.invalid_credentials')]);

    $this->post('/sharp/login', ['login' => 'test@example.org', 'password' => 'too-many'])
        ->assertSessionHasErrors('login');

    expect(session()->get('errors')->first('login'))
        ->toStartWith('Too many login attempts');

    expect(auth('sharp')->user())->toBeNull();
});

it('allows users to logout', function () {
    login();

    $this->post('/sharp/logout')
        ->assertRedirect('/sharp/login');

    expect(auth()->check())->toBeFalse();
});

it('handles custom auth check', function () {
    $this->app['config']->set(
        'sharp.auth.check_handler',
        fn () => new class implements SharpAuthenticationCheckHandler {
            public function check($user): bool
            {
                return $user->name == 'ok';
            }
        }
    );

    login(new User(['name' => 'ok']));

    $this->get('/sharp/s-list/person')
        ->assertOk();

    login(new User(['name' => 'ko']));

    $this->get('/sharp/s-list/person')
        ->assertRedirect('/sharp/login');
});

it('allows custom auth guard', function () {
    auth()->extend('test', function () {
        return new class implements \Illuminate\Contracts\Auth\Guard {
            protected $user;
            public function check()
            {
                return $this->user?->name === 'ok';
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
                return $this->user->id;
            }
            public function validate(array $credentials = [])
            {
            }
            public function hasUser()
            {
                return $this->user !== null;
            }
            public function setUser(\Illuminate\Contracts\Auth\Authenticatable $user)
            {
                $this->user = $user;
            }
        };
    });

    $this->app['config']->set('sharp.auth.guard', 'test');

    $this->app['config']->set(
        'auth.guards.test', [
            'driver' => 'test',
            'provider' => 'users',
        ],
    );

    login(new User(['name' => 'ok']));

    $this->get('/sharp/s-list/person')
        ->assertOk();

    login(new User(['name' => 'ko']));

    $this->get('/sharp/s-list/person')
        ->assertRedirect('/sharp/login');
});