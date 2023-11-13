<?php

namespace Code16\Sharp\Auth\TwoFactor;

use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class Sharp2faNotificationHandler implements Sharp2faHandler
{
    protected $user = null;

    public function generateCode(bool $remember = false): void
    {
        $code = $this->generateRandomCode();

        Session::put(
            $this->getSessionKey(),
            [
                'user_id' => $this->user->id,
                'code' => Hash::make($code),
                'remember' => $remember,
                'expires_at' => now()->addMinutes(15)->format('Y-m-d H:i:s'),
            ]
        );

        $this->user->notify($this->getNotification($code));
    }

    public function isExpectingLogin(): bool
    {
        return Session::has($this->getSessionKey())
            && (new Carbon(Session::get($this->getSessionKey())['expires_at']))?->isFuture();
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

    protected function getNotification(int $code): Notification
    {
        return new Sharp2faDefaultNotification($code);
    }

    protected function generateRandomCode(): int
    {
        return random_int(100000, 999999);
    }

    protected function getSessionKey(): string
    {
        return 'sharp:2fa:code';
    }
}
