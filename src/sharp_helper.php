<?php

/**
 * @return string
 */
function sharp_version()
{
    return \Code16\Sharp\SharpServiceProvider::VERSION;
}

/**
 * @return mixed
 */
function sharp_user()
{
    return auth()->guard(
        config("sharp.auth.guard", config("auth.defaults.guard"))
    )->user();
}

/**
 * @param string $ability
 * @param string $entityKey
 * @param string|null $instanceId
 * @return bool
 */
function sharp_has_ability(string $ability, string $entityKey, string $instanceId = null)
{
    try {
        sharp_check_ability($ability, $entityKey, $instanceId);
        return true;

    } catch(Code16\Sharp\Exceptions\Auth\SharpAuthorizationException $ex){
        return false;
    }
}

/**
 * @param string $ability
 * @param string $entityKey
 * @param string|null $instanceId
 * @throws Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
 */
function sharp_check_ability(string $ability, string $entityKey, string $instanceId = null)
{
    app(Code16\Sharp\Auth\SharpAuthorizationManager::class)
        ->check($ability, $entityKey, $instanceId);
}