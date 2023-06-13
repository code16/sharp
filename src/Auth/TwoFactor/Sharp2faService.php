<?php

namespace Code16\Sharp\Auth\TwoFactor;

interface Sharp2faService
{
    public function generateAndSendCodeFor($user, bool $remember = false): void;
    public function isExpectingLogin(): bool;
    public function checkCode(string $code): bool;
    public function forgetCode(): void;
    public function userId(): mixed;
    public function remember(): bool;
}