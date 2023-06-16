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
                'two_factor_recovery_codes' => $encryptedRecoveryCodes,
            ])
            ->save();
    }

    protected function getUserEncryptedSecret($userId): string
    {
        return $this->findUser($userId)?->two_factor_secret;
    }

    public function activate2faForUser(): void
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
            $this->user->email,
            decrypt($this->user->two_factor_secret)
        );
    }

    public function getRecoveryCodes(): array
    {
        return json_decode(decrypt($this->user->two_factor_recovery_codes));
    }

    public function checkUserRecoveryCode(mixed $userId, string $code): bool
    {
        $userCode = collect($this->setUser($this->findUser($userId))->getRecoveryCodes())
            ->first(function ($userCode) use ($code) {
                return hash_equals($userCode, $code);
            });
        
        if($userCode) {
            $this->user
                ->forceFill([
                    'two_factor_recovery_codes' => encrypt(
                        str_replace(
                            $userCode, 
                            '', 
                            decrypt($this->user->two_factor_recovery_codes)
                        )
                    ),
                ])
                ->save();
            
            return true;
        }
        
        return false;
    }
}
