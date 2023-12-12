<?php

namespace App\Sharp;

use Code16\Sharp\Auth\TwoFactor\Sharp2faNotificationHandler;

class Demo2faNotificationHandler extends Sharp2faNotificationHandler
{
    protected function generateRandomCode(): int
    {
        return 123456;
    }

    public function formHelpText(): string
    {
        return <<<'HTML'
                <div class="alert alert-info">
                    <div><a href="https://sharp.code16.fr/docs/guide/authentication.html#two-factor-authentication" target="_blank">Two-factor authentication</a> comes with two flavours: notification (mail, sms, etc) or TOTP authenticator app (like Google Authenticator).</div>
                    <div class="mt-2">This is a demo, expected code is <strong>123456</strong>.</div>
                </div>
                Please enter the 6-digit code
            HTML;
    }
}
