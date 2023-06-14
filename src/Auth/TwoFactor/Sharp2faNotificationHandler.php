<?php

namespace Code16\Sharp\Auth\TwoFactor;

use Carbon\Carbon;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class Sharp2faNotificationHandler implements Sharp2faHandler
{
    public function generateAndSendCodeFor($user, bool $remember = false): void
    {
        $code = $this->generateCode();
        Session::put(
            $this->getSessionKeyForCode(),
            [
                'user_id' => $user->id,
                'code' => Hash::make($code),
                'remember' => $remember,
                'expires_at' => now()->addMinutes(15)->format('Y-m-d H:i:s'),
            ]
        );

        $user->notify($this->getNotification($code));
    }

    public function isExpectingLogin(): bool
    {
        return Session::has($this->getSessionKeyForCode())
            && (new Carbon(Session::get($this->getSessionKeyForCode())['expires_at']))?->isFuture();
    }

    public function isEnabledFor($user): bool
    {
        return true;
    }

    public function checkCode(string $code): bool
    {
        return $this->isExpectingLogin()
            && Hash::check($code, Session::get($this->getSessionKeyForCode())['code']);
    }

    public function userId(): mixed
    {
        return Session::get($this->getSessionKeyForCode())['user_id'] ?? null;
    }

    public function remember(): bool
    {
        return Session::get($this->getSessionKeyForCode())['remember'] ?? false;
    }

    public function forgetCode(): void
    {
        Session::forget($this->getSessionKeyForCode());
    }

    protected function getNotification(int $code): Notification
    {
        return new Sharp2faDefaultNotification($code);
    }

    protected function generateCode(): int
    {
        return random_int(100000, 999999);
    }

    protected function getSessionKeyForCode(): string
    {
        return 'sharp:2fa:code';
    }
}
