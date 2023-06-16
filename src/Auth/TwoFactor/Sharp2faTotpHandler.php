<?php

namespace Code16\Sharp\Auth\TwoFactor;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

abstract class Sharp2faTotpHandler implements Sharp2faHandler
{
    protected $user = null;

    public function __construct(protected Google2FA $engine)
    {
    }
    
    public function generateCode(bool $remember = false): void
    {
        Session::put(
            $this->getSessionKey(),
            [
                'user_id' => $this->user->id,
                'remember' => $remember,
            ]
        );
    }

    public function isExpectingLogin(): bool
    {
        return Session::has($this->getSessionKey());
    }

    public function checkCode(string $code): bool
    {
        return $this->engine->verify(
            $code,
            decrypt($this->getUserEncryptedSecret($this->userId())),
        );
    }

    public function userId(): mixed
    {
        return $this->user?->id ?? (Session::get($this->getSessionKey())['user_id'] ?? null);
    }

    public function remember(): bool
    {
        return Session::get($this->getSessionKey())['remember'] ?? false;
    }

    public function forgetCode(): void
    {
        Session::forget($this->getSessionKey());
    }

    public function initialize(): void
    {
        $this->saveUserSecretAndRecoveryCodes(
            $this->user,
            encrypt($this->engine->generateSecretKey()),
            encrypt(
                json_encode(Collection::times(8, fn () => Str::random(10) . '-' . Str::random(10))->all())
            )
        );
    }

    protected function getSessionKey(): string
    {
        return 'sharp:2fa:code';
    }

    public function setUser($user): self
    {
        $this->user = $user;
        
        return $this;
    }

    abstract public function confirmUser(): void;
    
    abstract public function deactivate2faForUser(): void;

    abstract protected function saveUserSecretAndRecoveryCodes($user, string $encryptedSecret, string $encryptedRecoveryCodes): void;

    abstract protected function getUserEncryptedSecret($userId): string;
    
    abstract public function getQRCodeUrl(): string;
    
    abstract public function getRecoveryCodes(): array;
}
