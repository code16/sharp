<?php

use Code16\Sharp\Auth\SharpAuthenticationCheckHandler;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\User;
use Inertia\Testing\AssertableInertia as Assert;

beforeEach(function () {
    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );
});

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