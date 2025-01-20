<?php

namespace Code16\Sharp\Utils\Entities;

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
                app()->singleton($entity, fn () => (new $entity())->setEntityKey($entityKey));
            }

            return app($entity);
        }

        throw new SharpInvalidEntityKeyException("The entity [{$entityKey}] was not found.");
    }
}
