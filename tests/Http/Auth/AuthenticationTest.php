<?php

use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\TestAuthGuard;
use Code16\Sharp\Tests\Fixtures\User;

beforeEach(function () {
    sharp()->config()->addEntity('person', PersonEntity::class);
});

function setTestAuthGuard(): void
{
    auth()->extend('sharp', fn () => new TestAuthGuard());
    config()->set('auth.guards.sharp', ['driver' => 'sharp', 'provider' => 'users']);
    sharp()->config()->setAuthCustomGuard('sharp');
}

it('redirects guests to the login page', function () {
    $this->get('/sharp/s-list/person')
        ->assertRedirect(route('code16.sharp.login'));
});

it('redirects users from the login page to the home page', function () {
    login();

    $this->get(route('code16.sharp.login'))
        ->assertRedirect(route('code16.sharp.home'));
});

it('displays the login page', function () {
    $this->get(route('code16.sharp.login'))
        ->assertOk();
});

it('allows guests to login', function () {
    setTestAuthGuard();
    $this->withoutExceptionHandling();

    $this
        ->post(route('code16.sharp.login.post'), [
            'login' => 'test@example.org',
            'password' => 'password',
        ])
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth('sharp')->check())->toBeTrue();
});

it('does not allow to login with invalid payload', function () {
    $this->post(route('code16.sharp.login.post'))
        ->assertSessionHasErrors(['login', 'password']);

    $this->post(route('code16.sharp.login.post'), ['login' => 'bob@example.org'])
        ->assertSessionHasErrors(['password']);

    $this->post(route('code16.sharp.login.post'), ['password' => 'password'])
        ->assertSessionHasErrors(['login']);
});

it('handles remember_me option', function () {
    setTestAuthGuard();

    sharp()->config()->suggestRememberMeOnLoginForm();

    $this
        ->post(route('code16.sharp.login.post'), [
            'login' => 'test@example.org',
            'password' => 'password',
            'remember' => true,
        ])
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth('sharp')->user())->shouldRemember->toBeTrue();
});

it('does not allow remember_me option without proper config', function () {
    setTestAuthGuard();

    sharp()->config()->suggestRememberMeOnLoginForm(false);

    $this
        ->post(route('code16.sharp.login.post'), [
            'login' => 'test@example.org',
            'password' => 'password',
            'remember' => true,
        ])
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth('sharp')->user())->shouldRemember->toBeFalse();
});

it('hits rate limiter if configured', function () {
    setTestAuthGuard();

    sharp()->config()->enableLoginRateLimiting(1);

    $this->post(route('code16.sharp.login.post'), ['login' => 'test@example.org', 'password' => 'bad'])
        ->assertSessionHasErrors(['login' => trans('sharp::auth.invalid_credentials')]);

    $this->post(route('code16.sharp.login.post'), ['login' => 'test@example.org', 'password' => 'too-many'])
        ->assertSessionHasErrors('login');

    expect(session()->get('errors')->first('login'))
        ->toStartWith('Too many login attempts')
        ->and(auth('sharp')->user())->toBeNull();
});

it('allows users to logout', function () {
    login();

    $this->post(route('code16.sharp.logout'))
        ->assertRedirect();

    expect(auth()->check())->toBeFalse();
});

it('allows custom auth guard', function () {
    auth()->extend('test', function () {
        return new class() implements \Illuminate\Contracts\Auth\Guard
        {
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

            public function validate(array $credentials = []) {}

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

    sharp()->config()->setAuthCustomGuard('test');

    $this->app['config']->set(
        'auth.guards.test', [
            'driver' => 'test',
            'provider' => 'users',
        ]);

    login(new User(['name' => 'ok']));

    $this->get('/sharp/s-list/person')
        ->assertOk();

    login(new User(['name' => 'ko']));

    $this->get('/sharp/s-list/person')
        ->assertRedirect(route('code16.sharp.login'));
});
