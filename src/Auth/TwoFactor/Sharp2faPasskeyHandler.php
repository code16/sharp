<?php

namespace Code16\Sharp\Auth\TwoFactor;

use Code16\Sharp\Enums\MultiFactorMethod;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class Sharp2faPasskeyHandler implements Sharp2faHandler
{
    protected $user = null;

    public function generateCode(bool $remember = false): void
    {
        Session::put(
            $this->getSessionKey(),
            [
                'user_id' => $this->user->id,
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
        return $this->isExpectingLogin()
            && Hash::check($code, Session::get($this->getSessionKey())['code']);
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

    protected function getSessionKey(): string
    {
        return 'sharp:2fa:prompt-passkey';
    }

    public function method(): MultiFactorMethod
    {
        return MultiFactorMethod::Passkey;
    }
}
