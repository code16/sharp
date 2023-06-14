<?php

namespace Code16\Sharp\Auth\TwoFactor;

interface Sharp2faHandler
{
    public function generateAndSendCodeFor($user, bool $remember = false): void;

    public function isExpectingLogin(): bool;

    public function isEnabledFor($user): bool;

    public function checkCode(string $code): bool;

    public function forgetCode(): void;

    public function userId(): mixed;

    public function remember(): bool;
}
