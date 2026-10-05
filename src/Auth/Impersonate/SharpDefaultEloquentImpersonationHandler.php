<?php

namespace Code16\Sharp\Auth\Impersonate;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;

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
            ->when(
                Gate::has('viewSharp'),
                fn ($users) => $users->filter(fn ($user) => Gate::forUser($user)->allows('viewSharp'))
            )
            ->mapWithKeys(fn (Authenticatable $user) => [$user->getAuthIdentifier() => $user->$loginAttribute])
            ->all();
    }
}
