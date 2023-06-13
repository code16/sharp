<?php

namespace Code16\Sharp\Tests\Feature;

use Code16\Sharp\Auth\TwoFactor\Sharp2faDefaultNotification;
use Code16\Sharp\Auth\TwoFactor\Sharp2faServiceNotification;
use Code16\Sharp\Tests\Feature\Api\BaseApiTest;
use Code16\Sharp\Tests\Fixtures\TestAuthGuard;
use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Support\Facades\Notification;

class Login2faControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();

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
                'channel' => 'notification',
            ],
        );
    }

    /** @test */
    public function we_are_redirected_to_2fa_code_page_after_successful_first_step_login()
    {
        Notification::fake();

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
    public function a_2fa_notification_is_sent_to_the_user_after_first_step_login()
    {
        Notification::fake();

        $this
            ->from(route('code16.sharp.login'))
            ->post(route('code16.sharp.login.post'), ['login' => 'test@example.org', 'password' => 'password']);

        Notification::assertSentTo(
            new User(['email' => 'test@example.org']),
            Sharp2faDefaultNotification::class
        );
    }

    /** @test */
    public function we_can_login_with_the_correct_2fa_code()
    {
        $this->withoutExceptionHandling();
        Notification::fake();

        $this->app->bind(
            Sharp2faServiceNotification::class,
            fn () => new class extends Sharp2faServiceNotification
            {
                protected function generateCode(): int
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

        $this->assertEquals('test@example.org', auth('sharp')->user()->email);
    }

    /** @test */
    public function we_can_not_login_with_an_invalid_2fa_code()
    {
        Notification::fake();

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
