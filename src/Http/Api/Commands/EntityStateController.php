<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;

class EntityStateController extends ApiController
{
    use HandleCommandReturn;

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     * @throws SharpAuthorizationException
     */
    public function update($entityKey, $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        if(!$list->entityStateHandler()->authorize()
            || !$list->entityStateHandler()->authorizeFor($instanceId)) {
            throw new SharpAuthorizationException();
        }

        return $this->returnCommandResult(
            $list,
            array_merge(
                $list->entityStateHandler()->execute($instanceId, request()->only("value")),
                ["value" => request("value")]
            )
        );
    }
}