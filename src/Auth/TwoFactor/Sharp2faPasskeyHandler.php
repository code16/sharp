<?php

namespace Code16\Sharp\Auth\TwoFactor;

use Code16\Sharp\Enums\MultiFactorMethod;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Session;

class Sharp2faPasskeyHandler implements Sharp2faHandler
{
    protected ?Authenticatable $user = null;

    public function generateCode(bool $remember = false): void
    {
        Session::put(
            $this->getSessionKey(),
            [
                'user_id' => $this->user->getAuthIdentifier(),
                'remember' => $remember,
            ]
        );
    }

    public function isExpectingLogin(): bool
    {
        return Session::has($this->getSessionKey());
    }

    public function setUser($user): self
    {
        $this->user = $user;

        return $this;
    }

    public function isEnabledFor($user): bool
    {
        return true;
    }

    public function checkCode(string $code): bool
    {
        throw new \Exception('Passkey multi-factor is not based on code matching');
    }

    public function userId(): mixed
    {
        return Session::get($this->getSessionKey())['user_id'] ?? null;
    }

    public function remember(): bool
    {
        return Session::get($this->getSessionKey())['remember'] ?? false;
    }

    public function forgetCode(): void
    {
        Session::forget($this->getSessionKey());
    }

    public function formHelpText(): string
    {
        return trans('sharp::auth.2fa.passkey.form_help_text', [
            'email' => $this->user->{sharp()->config()->get('auth.login_attribute')} ?? null,
        ]);
    }

    protected function getSessionKey(): string
    {
        return 'sharp:2fa:prompt-passkey';
    }

    public function method(): MultiFactorMethod
    {
        return MultiFactorMethod::Passkey;
    }
}
