<?php

namespace Code16\Sharp\Auth\Impersonate;

class SharpDefaultEloquentImpersonationHandler extends SharpImpersonationHandler
{
    public function getUsers(): array
    {
        $guard = config('sharp.auth.guard') ?: config('auth.defaults.guard');
        $userModelClassName = config('auth.defaults.providers.'.$guard.'.model');
        $loginAttribute = config('sharp.auth.login_attribute');

        return $userModelClassName::query()
            ->orderBy($loginAttribute)
            ->get()
            ->mapWithKeys(fn ($user) => [$user->id => $user->$loginAttribute])
            ->all();
    }
}