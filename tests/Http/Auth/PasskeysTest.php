<?php

namespace Code16\Sharp\Tests\Http\Auth;

use Code16\Sharp\Http\Controllers\Auth\Passkeys\PasskeyController;
use Code16\Sharp\Http\Controllers\Auth\Passkeys\PasskeySkipPromptController;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\TestAuthGuard;
use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Route;
use Mockery;

beforeEach(function () {
    auth()->extend('sharp', fn () => new TestAuthGuard());
    config()->set('auth.guards.sharp', ['driver' => 'sharp', 'provider' => 'users']);

    sharp()->config()
        ->declareEntity(PersonEntity::class)
        ->setAuthCustomGuard('sharp')
        ->enablePasskeys();
});

it('sets passkeys.redirect session key in login show', function () {
    $this->get(route('code16.sharp.login'))
        ->assertOk();

    expect(session()->get('passkeys.redirect'))->toBe(route('code16.sharp.home'));
});

it('redirects to passkey creation after login if prompt is enabled, supported and no passkeys', function () {
    Route::get('/passkeys/create', [PasskeyController::class, 'create'])
        ->name('code16.sharp.passkeys.create');

    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('passkeys')->andReturn(
        Mockery::mock(Collection::class)->shouldReceive('count')->andReturn(0)->getMock()
    );

    $this->actingAs($user, 'sharp')
        ->post(route('code16.sharp.login.post'), [
            'login' => 'test@example.org',
            'password' => 'password',
            'supports_passkeys' => true,
        ])
        ->assertRedirect('/passkeys/create?prompt=1');
});

it('does not redirect to passkey creation if user already has passkeys', function () {
    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('passkeys')->andReturn(
        Mockery::mock(Collection::class)->shouldReceive('count')->andReturn(1)->getMock()
    );

    $this->actingAs($user, 'sharp')
        ->post(route('code16.sharp.login.post'), [
            'login' => 'test@example.org',
            'password' => 'password',
            'supports_passkeys' => true,
        ])
        ->assertRedirect(route('code16.sharp.home'));
});

it('does not redirect to passkey creation if browser does not support passkeys', function () {
    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('passkeys')->andReturn(
        Mockery::mock(Collection::class)->shouldReceive('count')->andReturn(0)->getMock()
    );

    $this->actingAs($user, 'sharp')
        ->post(route('code16.sharp.login.post'), [
            'login' => 'test@example.org',
            'password' => 'password',
            'supports_passkeys' => false,
        ])
        ->assertRedirect(route('code16.sharp.home'));
});

it('does not redirect to passkey creation if skip cookie is present', function () {
    $user = Mockery::mock(User::class)->makePartial();
    $user->shouldReceive('passkeys')->andReturn(
        Mockery::mock(Collection::class)->shouldReceive('count')->andReturn(0)->getMock()
    );

    $this->withCookie('sharp_skip_passkey_prompt', '1')
        ->actingAs($user, 'sharp')
        ->post(route('code16.sharp.login.post'), [
            'login' => 'test@example.org',
            'password' => 'password',
            'supports_passkeys' => true,
        ])
        ->assertRedirect(route('code16.sharp.home'));
});

it('sets skip cookie in PasskeySkipPromptController', function () {
    Route::post('/passkeys/skip-prompt', PasskeySkipPromptController::class)
        ->name('code16.sharp.passkeys.skip-prompt');

    $user = new User(['email' => 'test@example.org']);

    $this->actingAs($user, 'sharp')
        ->post('/passkeys/skip-prompt')
        ->assertRedirect(route('code16.sharp.home'))
        ->assertCookie('sharp_skip_passkey_prompt', true);
});
