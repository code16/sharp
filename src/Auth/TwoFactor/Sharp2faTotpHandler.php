<?php

namespace Code16\Sharp\Auth\TwoFactor;

use Code16\Sharp\Auth\TwoFactor\Engines\Sharp2faTotpEngine;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

abstract class Sharp2faTotpHandler implements Sharp2faHandler
{
    protected $user = null;

    public function __construct(protected Sharp2faTotpEngine $engine)
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
        $isCodeValid = $this->engine->verify(
            $code,
            decrypt($this->getUserEncryptedSecret($this->userId())),
        );
        
        if (!$isCodeValid) {
            return $this->checkUserRecoveryCode($this->userId(), $code);
        }
        
        return true;
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
                json_encode(Collection::times(8, fn () => Str::random(10).'-'.Str::random(10))->all())
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

    public function formHelpText(): string
    {
        return trans('sharp::auth.2fa.totp.form_help_text');
    }

    abstract public function activate2faForUser(): void;

    abstract public function deactivate2faForUser(): void;

    abstract protected function saveUserSecretAndRecoveryCodes($user, string $encryptedSecret, string $encryptedRecoveryCodes): void;

    abstract protected function getUserEncryptedSecret($userId): string;

    abstract public function getQRCodeUrl(): string;
    
    abstract public function getRecoveryCodes(): array;

    abstract protected function checkUserRecoveryCode(mixed $userId, string $code): bool;
}
