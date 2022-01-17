<?php

function currentSharpRequest(): \Code16\Sharp\Http\Context\CurrentSharpRequest
{
    return app(\Code16\Sharp\Http\Context\CurrentSharpRequest::class);
}

function sharp_version(): string
{
    return \Code16\Sharp\SharpServiceProvider::VERSION;
}

/**
 * @return mixed
 */
function sharp_user()
{
    return auth()->user();
}

function sharp_has_ability(string $ability, string $entityKey, string $instanceId = null): bool
{
    return app(Code16\Sharp\Auth\SharpAuthorizationManager::class)
        ->isAllowed($ability, sharp_normalize_entity_key($entityKey)[0], $instanceId);
}

function sharp_check_ability(string $ability, string $entityKey, string $instanceId = null)
{
    app(Code16\Sharp\Auth\SharpAuthorizationManager::class)
        ->check($ability, sharp_normalize_entity_key($entityKey)[0], $instanceId);
}

function sharp_normalize_entity_key(string $entityKey): array
{
    $parts = explode(':', $entityKey);

    return count($parts) == 1 ? [$parts[0], null] : $parts;
}

function sharp_base_url_segment(): string
{
    return config('sharp.custom_url_segment', 'sharp');
}

/**
 * Return true if the $handler class actually implements the $methodName method;
 * return false if the method is defined as concrete in a super class and not overridden.
 */
function is_method_implemented_in_concrete_class($handler, string $methodName): bool
{
    try {
        $foo = new \ReflectionMethod(get_class($handler), $methodName);
        $declaringClass = $foo->getDeclaringClass()->getName();

        return $foo->getPrototype()->getDeclaringClass()->getName() !== $declaringClass;
    } catch (\ReflectionException $e) {
        return false;
    }
}
