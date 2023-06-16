<?php

namespace Code16\Sharp\Auth\TwoFactor\Engines;

use PragmaRX\Google2FA\Google2FA;

class GoogleTotpEngine implements Sharp2faTotpEngine
{
    public function __construct(protected Google2FA $google2fa)
    {
    }

    public function verify(string $code, string $secret): bool
    {
        return $this->google2fa->verify($secret, $code);
    }

    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    public function getQRCodeUrl(string $email, string $secret): string
    {
        return $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $email,
            $secret,
        );
    }
}