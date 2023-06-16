<?php

namespace Code16\Sharp\Auth\TwoFactor;

use Illuminate\Database\Eloquent\Model;

class Sharp2faEloquentDefaultTotpHandler extends Sharp2faTotpHandler
{
    public function isEnabledFor($user): bool
    {
        return $user->two_factor_secret !== null
            && $user->two_factor_confirmed_at !== null;
    }

    protected function saveUserSecretAndRecoveryCodes($user, string $encryptedSecret, string $encryptedRecoveryCodes): void
    {
        $user
            ->forceFill([
                'two_factor_secret' => $encryptedSecret,
                'two_factor_recovery_codes' => $encryptedRecoveryCodes
            ])
            ->save();
    }

    protected function getUserEncryptedSecret($userId): string
    {
        return $this->findUser($userId)?->two_factor_secret;
    }

    public function confirmUser(): void
    {
        $this->user
            ->forceFill([
                'two_factor_confirmed_at' => now(),
            ])
            ->save();
    }

    public function deactivate2faForUser(): void
    {
        $this->user
            ->forceFill([
                'two_factor_secret' => null,
                'two_factor_recovery_codes' => null,
                'two_factor_confirmed_at' => null,
            ])
            ->save();
    }

    protected function findUser($userId): Model
    {
        $userClass = class_exists("App\Models\User") ? "App\Models\User" : "App\User";
        
        return app($userClass)->find($userId);
    }

    public function getQRCodeUrl(): string
    {
        return $this->engine->getQRCodeUrl(
            config('app.name'), 
            $this->user->email, 
            decrypt($this->user->two_factor_secret)
        );
    }

    public function getRecoveryCodes(): array
    {
        return json_decode(decrypt($this->user->two_factor_recovery_codes));
    }
}
