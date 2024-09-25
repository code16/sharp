<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Exceptions\SharpMethodNotImplementedException;

class ApiEntityListController extends ApiController
{
    /**
     * Reorder instances.
     */
    public function update(string $entityKey)
    {
        sharp_check_ability('entity', $entityKey);

        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        $list->reorderHandler()->reorder(request('instances'));

        return response()->json([
            'ok' => true,
        ]);
    }

    /**
     * Delete an instance.
     */
    public function delete(string $entityKey, string $instanceId)
    {
        sharp_check_ability('delete', $entityKey, $instanceId);

        $impl = $this->getListInstance($entityKey);
        if (! self::isDeleteMethodImplementedInConcreteClass($impl)) {
            // Try to delete from Show Page
            try {
                $impl = $this->getShowInstance($entityKey);
            } catch (SharpInvalidEntityKeyException $ex) {
                // No Show Page implementation was defined for this entity
                throw new SharpMethodNotImplementedException('The delete() method is not implemented, neither in the Entity List nor in the Show Page');
            }
        }

        $impl->delete($instanceId);

        return response()->json([
            'ok' => true,
        ]);
    }

    private static function isDeleteMethodImplementedInConcreteClass(SharpEntityList $impl): bool
    {
        try {
            $foo = new \ReflectionMethod(get_class($impl), 'delete');
            $declaringClass = $foo->getDeclaringClass()->getName();

            return $foo->getPrototype()->getDeclaringClass()->getName() !== $declaringClass;
        } catch (\ReflectionException) {
            return false;
        }
    }
}
