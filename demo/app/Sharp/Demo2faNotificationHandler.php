<?php

namespace App\Sharp;

use Code16\Sharp\Auth\TwoFactor\Sharp2faNotificationHandler;

class Demo2faNotificationHandler extends Sharp2faNotificationHandler
{
    public function isEnabledFor($user): bool
    {
        return $user->email === 'editor@example.org';
    }

    protected function generateRandomCode(): int
    {
        return 123456;
    }

    public function formHelpText(): string
    {
        return <<<'HTML'
                <div style="color: #999">This user has configured a two-factor authentication (<a href="https://sharp.code16.fr/docs/guide/authentication.html#two-factor-authentication" style="text-decoration: underline" target="_blank">see documentation</a>).</div>
                <div style="color: #999; margin: .5em 0">Code was set to <strong>123456</strong> for this demo.</div>
                <div>Please enter the 6-digit code</div>
            HTML;
    }
}
