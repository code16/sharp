<?php

namespace Code16\Sharp\Auth\TwoFactor\Engines;

interface Sharp2faTotpEngine
{
    public function verify(string $code, string $secret): bool;

    public function generateSecretKey(): string;

    public function getQRCodeUrl(string $email, string $secret): string;
}
