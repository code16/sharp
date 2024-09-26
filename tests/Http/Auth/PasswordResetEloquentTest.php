<?php

namespace Tests\Feature\Auth;

use Code16\Sharp\Tests\Fixtures\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Schema;

uses(LazilyRefreshDatabase::class);

beforeEach(function () {
    $this->withoutExceptionHandling();
    Notification::fake();

    Schema::create('users', function (Blueprint $table) {
        $table->increments('id');
        $table->string('email');
        $table->string('password')->nullable();
        $table->string('remember_token')->nullable();
        $table->timestamps();
    });

    Schema::create('password_reset_tokens', function (Blueprint $table) {
        $table->string('email')->index();
        $table->string('token');
        $table->timestamp('created_at')->nullable();
    });

    config()->set('auth.providers.users.model', User::class);
});

it('allows to request a reset password link', function () {
    $user = User::create(['email' => fake()->email]);

    $this->get(route('code16.sharp.password.request'));

    $this->post(route('code16.sharp.password.request.post'), ['email' => $user->email])
        ->assertRedirect(route('code16.sharp.password.request'))
        ->assertSessionHas('status', __('sharp::passwords.sent'));

    Notification::assertSentTo($user, ResetPassword::class);
});

it('allows to display password screen', function () {
    $user = User::create(['email' => fake()->email]);

    $this->post(route('code16.sharp.password.request.post'), ['email' => $user->email])
        ->assertRedirect();

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
        $this->get(route('code16.sharp.password.reset', $notification->token))
            ->assertOk();

        return true;
    });
});

it('allows to reset password with valid token', function () {
    $user = User::create(['email' => fake()->email]);

    $this->post(route('code16.sharp.password.request.post'), ['email' => $user->email]);

    Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user) {
        $this
            ->post(route('code16.sharp.password.reset.post'), [
                'email' => $user->email,
                'token' => $notification->token,
                'password' => 'password',
                'password_confirmation' => 'password',
            ])
            ->assertRedirect(route('code16.sharp.login'))
            ->assertSessionHasNoErrors();

        return true;
    });
});
