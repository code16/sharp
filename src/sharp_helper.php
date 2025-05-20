<?php

use Illuminate\Support\Str;

function sharp(): \Code16\Sharp\Utils\SharpUtil
{
    return app(\Code16\Sharp\Utils\SharpUtil::class);
}

/**
 * @deprecated use sharp()->context() instead
 */
function currentSharpRequest(): \Code16\Sharp\Http\Context\CurrentSharpRequest
{
    return app(\Code16\Sharp\Http\Context\CurrentSharpRequest::class);
}

function instanciate($class)
{
    return is_string($class) ? app($class) : value($class);
}

function sharp_has_ability(string $ability, string $entityKey, ?string $instanceId = null): bool
{
    return app(Code16\Sharp\Auth\SharpAuthorizationManager::class)
        ->isAllowed($ability, Str::before($entityKey, ':'), $instanceId);
}

function sharp_check_ability(string $ability, string $entityKey, ?string $instanceId = null)
{
    app(Code16\Sharp\Auth\SharpAuthorizationManager::class)
        ->check($ability, Str::before($entityKey, ':'), $instanceId);
}
