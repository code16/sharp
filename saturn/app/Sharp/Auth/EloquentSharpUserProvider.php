<?php

namespace App\Sharp\Auth;

use Illuminate\Auth\EloquentUserProvider;

class EloquentSharpUserProvider extends EloquentUserProvider
{

    /**
     * Retrieve a user by their unique identifier.
     *
     * @param  mixed  $identifier
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveById($identifier)
    {
        $user = parent::retrieveById($identifier);

        return $user && $user->hasGroup('sharp') ? $user : null;
    }

    /**
     * Retrieve a user by the given credentials.
     *
     * @param  array  $credentials
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function retrieveByCredentials(array $credentials)
    {
        $user = parent::retrieveByCredentials($credentials);

        return $user && $user->hasGroup('sharp') ? $user : null;
    }
}