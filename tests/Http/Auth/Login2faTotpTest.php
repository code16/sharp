<?php

use Code16\Sharp\Auth\TwoFactor\Engines\Sharp2faTotpEngine;
use Code16\Sharp\Auth\TwoFactor\Sharp2faTotpHandler;
use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Code16\Sharp\Tests\Fixtures\TestAuthGuard;

beforeEach(function () {
    auth()->extend('sharp', fn () => new TestAuthGuard());
    config()->set('auth.guards.sharp', ['driver' => 'sharp', 'provider' => 'users']);

    app()->bind(
        Sharp2faTotpEngine::class,
        fn () => new class() implements Sharp2faTotpEngine
        {
            public function verify(string $code, string $secret): bool
            {
                return $code === '123456';
            }

            public function generateSecretKey(): string
            {
                return 'secret';
            }

            public function getQRCodeUrl(string $email, string $secret): string
            {
                return '';
            }
        }
    );

    sharp()->config()
        ->declareEntity(PersonEntity::class)
        ->setAuthCustomGuard('sharp')
        ->enable2faCustom(new class(app(Sharp2faTotpEngine::class)) extends Sharp2faTotpHandler
        {
            public function isEnabledFor($user): bool
            {
                return true;
            }

            public function activate2faForUser(): void {}

            public function deactivate2faForUser(): void {}

            protected function saveUserSecretAndRecoveryCodes(
                $user,
                string $encryptedSecret,
                string $encryptedRecoveryCodes
            ): void {}

            protected function getUserEncryptedSecret($userId): string
            {
                return encrypt('secret');
            }

            public function getQRCodeUrl(): string
            {
                return '';
            }

            public function getRecoveryCodes(): array
            {
                return ['code1', 'code2'];
            }

            protected function checkUserRecoveryCode(mixed $userId, string $code): bool
            {
                return in_array($code, ['code1', 'code2']);
            }
        });
});

it('redirects to 2fa code page after successful first step login', function () {
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

it('logs in the user after successful 2fa code validation', function () {
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

it('logs in the user after entering a valid recovery code', function () {
    $this
        ->post(
            route('code16.sharp.login.post'),
            ['login' => 'test@example.org', 'password' => 'password']
        )
        ->assertRedirect(route('code16.sharp.login.2fa'));

    $this
        ->post(
            route('code16.sharp.login.2fa.post'),
            ['code' => 'code1']
        )
        ->assertRedirect(route('code16.sharp.home'));

    expect(auth('sharp')->user()->email)->toBe('test@example.org');
});

it('does not log in the user after invalid 2fa code validation', function () {
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
