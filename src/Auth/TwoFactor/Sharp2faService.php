<?php

namespace Code16\Sharp\Auth\TwoFactor;

interface Sharp2faService
{
    public function generateAndSendCodeFor($user): void;
    public function isExpectingLogin(): bool;
    public function checkCode(string $code): bool;
    public function forgetCode(): void;
}