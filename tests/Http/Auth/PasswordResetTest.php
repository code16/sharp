<?php

namespace Tests\Feature\Auth;

use Code16\Sharp\Tests\Fixtures\TestPasswordBroker;

beforeEach(function () {
    config()->set('sharp.auth.forgotten_password.password_broker', TestPasswordBroker::class);
});

it('allows to display password link screen', function () {
    $this->get(route('code16.sharp.password.request'))
        ->assertOk();
});

it('allows to request a reset password link', function () {
    $this->get(route('code16.sharp.password.request'));

    $this->post(route('code16.sharp.password.request.post'), ['email' => 'test@example.org'])
        ->assertRedirect(route('code16.sharp.password.request'))
        ->assertSessionHas('status', __('sharp::passwords.sent'));
});

it('allows to reset password', function () {
    config()->set(
        'sharp.auth.forgotten_password.reset_password_callback',
        function ($user, $password) {
            throw_if(
                $user->email !== 'test@example.org' || $password !== 'password',
                new \Exception('Invalid credentials')
            );
        }
    );

    $this
        ->post(route('code16.sharp.password.reset.post'), [
            'email' => 'test@example.org',
            'token' => 'my-token',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])
        ->assertRedirect(route('code16.sharp.login'))
        ->assertSessionHasNoErrors();
});
