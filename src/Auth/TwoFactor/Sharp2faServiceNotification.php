<?php

namespace Code16\Sharp\Auth\TwoFactor;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;

class Sharp2faServiceNotification implements Sharp2faService
{
    public function generateAndSendCodeFor($user): void
    {
        $code = $this->generateCode();
        Session::put($this->getSessionKeyForCode(), Hash::make($code));
        
        $notificationClass = config('sharp.auth.2fa.notification_class', Sharp2faDefaultNotification::class);
        $user->notify(new $notificationClass($code));
    }

    public function isExpectingLogin(): bool
    {
        return Session::has($this->getSessionKeyForCode());
    }

    public function checkCode(string $code): bool
    {
        return $this->isExpectingLogin()
            && Hash::check($code, Session::get($this->getSessionKeyForCode()));
    }

    public function forgetCode(): void
    {
        Session::forget($this->getSessionKeyForCode());
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