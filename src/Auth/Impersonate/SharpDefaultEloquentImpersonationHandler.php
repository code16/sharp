<?php

namespace Code16\Sharp\Auth\Impersonate;

class SharpDefaultEloquentImpersonationHandler extends SharpImpersonationHandler
{
    public function getUsers(): array
    {
        $guard = sharp()->config()->get('auth.guard') ?: config('auth.defaults.guard');
        $userProvider = config('auth.guards.'.$guard.'.provider');
        $userModelClassName = config('auth.providers.'.$userProvider.'.model');
        $loginAttribute = sharp()->config()->get('auth.login_attribute');

        return $userModelClassName::query()
            ->orderBy($loginAttribute)
            ->get()
            ->mapWithKeys(fn($user) => [$user->id => $user->$loginAttribute])
            ->all();
    }
}
