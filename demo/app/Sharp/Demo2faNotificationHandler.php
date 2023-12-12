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
                    <div>Two-factor authentication based on email notifications has been enabled for all users.</div>
                    <div class="mt-2">Expected code is <strong>123456</strong>.</div>
                    <div class="mt-2">It is also possible to set an authentification based on a TOTP authenticator app (like Google or Microsoft Authenticator). Check the <a href="https://sharp.code16.fr/docs/guide/authentication.html#two-factor-authentication" target="_blank">documentation</a>.</div>
                </div>
                An email has been sent to your address. Please enter the 6-digit code.
            HTML;
    }
}
