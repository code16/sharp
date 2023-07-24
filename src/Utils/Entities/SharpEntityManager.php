<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;

class SharpEntityManager
{
    public function entityFor(string $entityKey): SharpEntity|SharpDashboardEntity
    {
        $entityKey = sharp_normalize_entity_key($entityKey)[0];

        if (is_array(config('sharp.entities'))) {
            $entity = config("sharp.entities.$entityKey") ?: config("sharp.dashboards.$entityKey");
        } elseif (class_exists(config('sharp.entities'))) {
            // A custom SharpEntityResolver is used
            $sharpEntityResolver = config('sharp.entities');
            if (! app()->bound($sharpEntityResolver)) {
                app()->singleton($sharpEntityResolver, $sharpEntityResolver);
            }
            if (! ($resolver = app($sharpEntityResolver)) instanceof SharpEntityResolver) {
                throw new SharpInvalidEntityKeyException($sharpEntityResolver.' is an invalid entity resolver class: it should extend '.SharpEntityResolver::class.'.');
            }

            $entity = $resolver->entityClassName($entityKey);
        }

        if (isset($entity)) {
            if (! app()->bound($entity)) {
                app()->singleton($entity, function () use ($entity, $entityKey) {
                    return (new $entity())->setEntityKey($entityKey);
                });
            }

            return app($entity);
        }

        throw new SharpInvalidEntityKeyException("The entity [{$entityKey}] was not found.");
    }
}
