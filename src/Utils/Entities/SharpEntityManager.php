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
            $entity = sharp()->config()->get('entities.'.$entityKey);
            if (! $entity) {
                // Legacy dashboard configuration (to be removed in 10.x)
                $entity = sharp()->config()->get('dashboards.'.$entityKey);
            }
        } elseif ($sharpEntityResolver = sharp()->config()->get('entity_resolver')) {
            // A custom SharpEntityResolver is used
            if (! app()->bound(get_class($sharpEntityResolver))) {
                app()->singleton(get_class($sharpEntityResolver), fn () => $sharpEntityResolver);
            }

            $entity = $sharpEntityResolver->entityClassName($entityKey);
        }

        if (isset($entity)) {
            if (! app()->bound($entity)) {
                app()->singleton($entity);
            }

            return app($entity);
        }

        throw new SharpInvalidEntityKeyException("The entity for key [{$entityKey}] was not found.");
    }

    public function entityKeyFor(string|BaseSharpEntity $entity): string
    {
        if (is_string($entity) && ! class_exists($entity)) {
            // Should be an entity key
            return $entity;
        }

        $entityClassName = is_string($entity) ? $entity : get_class($entity);
        $entities = sharp()->config()->get('entities');

        if (! is_array($entities) || ($entityKey = array_search($entityClassName, $entities)) === false) {
            throw new SharpInvalidConfigException(
                sprintf(
                    'Can’t find entityKey for [%s] (warning: this can’t work with an Entity Resolver).',
                    $entityClassName
                )
            );
        }

        return $entityKey;
    }
}
