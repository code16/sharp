<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;

class SharpEntityManager
{
    public function entityFor(string $entityKey): SharpEntity|SharpDashboardEntity
    {
        $entityKey = sharp_normalize_entity_key($entityKey)[0];

        if (count(sharpConfig()->get('entities')) > 0) {
            $entity = sharpConfig()->get('entities.' . $entityKey) ?: sharpConfig()->get('dashboards.' . $entityKey);
        } elseif ($sharpEntityResolver = sharpConfig()->get('entity_resolver')) {
            // A custom SharpEntityResolver is used
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
