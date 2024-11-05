<?php

use Code16\Sharp\Auth\TwoFactor\Sharp2faDefaultNotification;
use Code16\Sharp\Auth\TwoFactor\Sharp2faNotificationHandler;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\TestAuthGuard;
use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Support\Facades\Notification;

beforeEach(function () {
    auth()->extend('sharp', fn () => new TestAuthGuard());

    sharp()->config()->addEntity('person', PersonEntity::class)
        ->setAuthCustomGuard('sharp')
        ->enable2faByNotification();

    config()->set('auth.guards.sharp', ['driver' => 'sharp', 'provider' => 'users']);
});

it('redirects to 2fa code page after successful first step login', function () {
    Notification::fake();

    $this->post(route('code16.sharp.login.post'), ['login' => 'test@example.org', 'password' => 'password'])
        ->assertRedirect(route('code16.sharp.login.2fa'));
});

it('does not redirect to 2fa code page after failed first step login', function () {
    $this
        ->from(route('code16.sharp.login'))
        ->post(route('code16.sharp.login.post'), ['login' => 'test@example.org', 'password' => 'bad'])
        ->assertSessionHasErrors('login')
        ->assertRedirect(route('code16.sharp.login'));
});

it('sends to the user a 2fa notification after successful first step login', function () {
    Notification::fake();

    $this->post(
        route('code16.sharp.login.post'),
        ['login' => 'test@example.org', 'password' => 'password']
    );

    Notification::assertSentTo(
        new User(['email' => 'test@example.org']),
        Sharp2faDefaultNotification::class
    );
});

it('logs in the user after successful 2fa code validation', function () {
    Notification::fake();

    sharp()->config()->enable2faCustom(
        new class() extends Sharp2faNotificationHandler
        {
            protected function generateRandomCode(): int
            {
                return 123456;
            }
        }
    );

    $this
        ->post(
            route('code16.sharp.login.post'),
            ['login' => 'test@example.org', 'password' => 'password']
        )
        ->assertRedirect(route('code16.sharp.login.2fa'));

    $this
        ->post(
            route('code16.sharp.login.2fa.post'),
            ['code' => 123456]
        )
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth('sharp')->user()->email)->toBe('test@example.org');
});

it('does not log in the user after invalid 2fa code validation', function () {
    Notification::fake();

    $this
        ->post(
            route('code16.sharp.login.post'),
            ['login' => 'test@example.org', 'password' => 'password']
        )
        ->assertRedirect(route('code16.sharp.login.2fa'));

    $this
        ->from(route('code16.sharp.login.2fa'))
        ->post(
            route('code16.sharp.login.2fa.post'),
            ['code' => 'bad']
        )
        ->assertSessionHasErrors(['code' => trans('sharp::auth.2fa.invalid')])
        ->assertRedirect(route('code16.sharp.login.2fa'));
});

it('throttles the user after to many attempts with invalid 2fa code validation', function () {
    for ($k = 0; $k < 3; $k++) {
        $this
            ->from(route('code16.sharp.login.2fa'))
            ->post(
                route('code16.sharp.login.2fa.post'),
                ['code' => 'bad']
            );
    }

    $this
        ->from(route('code16.sharp.login.2fa'))
        ->post(
            route('code16.sharp.login.2fa.post'),
            ['code' => 'bad']
        )
        ->assertSessionHasErrors('code')
        ->assertRedirect(route('code16.sharp.login.2fa'));

    expect(session()->get('errors')->first('code'))->toStartWith('Too many login attempts');
    expect(auth('sharp')->user())->toBeNull();
});
