<?php

namespace Code16\Sharp\Auth\TwoFactor;

use Illuminate\Database\Eloquent\Model;

class Sharp2faEloquentDefaultTotpHandler extends Sharp2faTotpHandler
{
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

    public function confirmUser($user): void
    {
        $user
            ->forceFill([
                'two_factor_confirmed_at' => now(),
            ])
            ->save();
    }

    protected function findUser($userId): Model
    {
        $userClass = class_exists("App\Models\User") ? "App\Models\User" : "App\User";
        
        return app($userClass)->find($userId);
    }

    public function getQRCodeUrl($user): string
    {
        return $this->engine->getQRCodeUrl(
            config('app.name'), 
            $user->email, 
            decrypt($user->two_factor_secret)
        );
    }
}
