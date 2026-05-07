<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\SharpInvalidEntityKeyException;
use Code16\Sharp\Exceptions\SharpMethodNotImplementedException;
use Code16\Sharp\Http\Controllers\Controller;

class ApiEntityListController extends Controller
{
    /**
     * Reorder instances.
     */
    public function update(string $globalFilter, string $entityKey)
    {
        $this->authorizationManager->check('entity', $entityKey);

        $list = $this->entityManager->entityFor($entityKey)->getListOrFail();
        $list->buildListConfig();

        $list->reorderHandler()->reorder(request('instances'));

        return response()->json([
            'ok' => true,
        ]);
    }

    /**
     * Delete an instance.
     */
    public function delete(string $globalFilter, string $entityKey, string $instanceId)
    {
        $this->authorizationManager->check('delete', $entityKey, $instanceId);

        $list = $this->entityManager->entityFor($entityKey)->getListOrFail();
        $list->initQueryParams(request()->query());

        if (self::isDeleteMethodImplementedInConcreteClass($list)) {
            $list->delete($instanceId);
        } else {
            try {
                $show = $this->entityManager->entityFor($entityKey)->getShowOrFail();
                $show->delete($instanceId);
            } catch (SharpInvalidEntityKeyException $ex) {
                // No Show Page implementation was defined for this entity
                throw new SharpMethodNotImplementedException('The delete() method is not implemented, neither in the Entity List nor in the Show Page');
            }
        }

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
