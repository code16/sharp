<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Exceptions\SharpInvalidConfigException;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Illuminate\Support\Str;

class SharpEntityManager
{
    public function entityFor(string $entityKey): SharpEntity|SharpDashboardEntity
    {
        $entityKey = Str::before($entityKey, ':');

        if (count(sharp()->config()->get('entities')) > 0) {
            $entityClass = sharp()->config()->get('entities.'.$entityKey);
            if (! $entityClass) {
                // Legacy dashboard configuration (to be removed in 10.x)
                $entityClass = sharp()->config()->get('dashboards.'.$entityKey);
            }
        } elseif ($sharpEntityResolver = sharp()->config()->get('entity_resolver')) {
            // A custom SharpEntityResolver is used
            if (! app()->bound(get_class($sharpEntityResolver))) {
                app()->singleton(get_class($sharpEntityResolver), fn () => $sharpEntityResolver);
            }

            $entityClass = $sharpEntityResolver->entityClassName($entityKey);
        }

        if (isset($entityClass)) {
            if (! app()->bound($entityClass)) {
                // Optimization: resolve each entity only once per request
                app()->singleton($entityClass);
            }

            return app($entityClass);
        }

        throw new SharpInvalidEntityKeyException("No entity with entity key [{$entityKey}] was found.");
    }

    public function entityKeyFor(string|BaseSharpEntity $entity): string
    {
        if (is_string($entity) && ! class_exists($entity)) {
            // Should already be an entity key
            return $entity;
        }

        $entityClassName = is_string($entity) ? $entity : get_class($entity);
        $entities = sharp()->config()->get('entities');

        if (! is_array($entities) || ($entityKey = array_search($entityClassName, $entities)) === false) {
            throw new SharpInvalidConfigException(
                sprintf(
                    'Can’t find entity key for [%s] (warning: this can’t work with an Entity Resolver).',
                    $entityClassName
                )
            );
        }

        return $entityKey;
    }
}
