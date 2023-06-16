<?php

namespace Code16\Sharp\Tests\Feature\Auth;

use Code16\Sharp\Auth\TwoFactor\Engines\Sharp2faTotpEngine;
use Code16\Sharp\Auth\TwoFactor\Sharp2faTotpHandler;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\TestAuthGuard;

class LoginTotp2faControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->app->bind(
            Sharp2faTotpEngine::class,
            fn() => new class implements Sharp2faTotpEngine {
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

        auth()->extend('sharp', fn () => new TestAuthGuard());
        $this->app['config']->set(
            'auth.guards.sharp', [
                'driver' => 'sharp',
                'provider' => 'users',
            ],
        );
        $this->app['config']->set(
            'sharp.auth.guard', 'sharp'
        );
        $this->app['config']->set(
            'sharp.auth.2fa', [
                'enabled' => true,
                'handler' => fn() => new class(app(Sharp2faTotpEngine::class)) extends Sharp2faTotpHandler {
                    public function isEnabledFor($user): bool
                    {
                        return true;
                    }
                    public function activate2faForUser(): void
                    {
                    }
                    public function deactivate2faForUser(): void
                    {
                    }
                    protected function saveUserSecretAndRecoveryCodes($user, string $encryptedSecret, string $encryptedRecoveryCodes): void
                    {
                    }
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
                },
            ],
        );
    }

    /** @test */
    public function we_are_redirected_to_2fa_code_page_after_successful_first_step_login()
    {
        $this->withoutExceptionHandling();
        $this
            ->from(route('code16.sharp.login'))
            ->post(route('code16.sharp.login.post'), ['login' => 'test@example.org', 'password' => 'password'])
            ->assertRedirect(route('code16.sharp.login.2fa'));
    }

    /** @test */
    public function we_are_not_redirected_to_2fa_code_page_after_failed_login()
    {
        $this
            ->from(route('code16.sharp.login'))
            ->post(route('code16.sharp.login.post'), ['login' => 'test@example.org', 'password' => 'bad-pwd'])
            ->assertSessionHasErrors('login')
            ->assertRedirect(route('code16.sharp.login'));
    }

    /** @test */
    public function we_can_login_with_the_correct_2fa_code()
    {
        $this->withoutExceptionHandling();

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

        $this->assertEquals('test@example.org', auth('sharp')->user()->email);
    }

    /** @test */
    public function we_can_login_with_a_valid_2fa_recovery_code()
    {
        $this->withoutExceptionHandling();

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

        $this->assertEquals('test@example.org', auth('sharp')->user()->email);
    }

    /** @test */
    public function we_can_not_login_with_an_invalid_2fa_code()
    {
        $this
            ->post(
                route('code16.sharp.login.post'),
                ['login' => 'test@example.org', 'password' => 'password']
            )
            ->assertRedirect(route('code16.sharp.login.2fa'));

        for ($k = 0; $k < 3; $k++) {
            $this
                ->from(route('code16.sharp.login.2fa'))
                ->post(
                    route('code16.sharp.login.2fa.post'),
                    ['code' => 'bad']
                )
                ->assertSessionHasErrors(['code' => trans('sharp::auth.2fa.invalid')])
                ->assertRedirect(route('code16.sharp.login.2fa'));
        }

        $this
            ->from(route('code16.sharp.login.2fa'))
            ->post(
                route('code16.sharp.login.2fa.post'),
                ['code' => 'bad']
            )
            ->assertSessionHasErrors('code')
            ->assertRedirect(route('code16.sharp.login.2fa'));

        $this->assertStringStartsWith('Too many login attempts', session()->get('errors')->first('code'));
    }
}
