<?php

namespace App\Sharp;

use Code16\Sharp\Auth\TwoFactor\Sharp2faNotificationHandler;

class Demo2faNotificationHandler extends Sharp2faNotificationHandler
{
    public function isEnabledFor($user): bool
    {
        return $user->email === 'editor@example.org';
    }

    protected function generateCode(): int
    {
        return 123456;
    }

    public function formHelpText(): string
    {
        return <<<HTML
            <div>This user has configured a two-factor authentication.</div>
            <div class="mt-2">Code was set to <strong>123456</strong></div>
        HTML;
    }
}