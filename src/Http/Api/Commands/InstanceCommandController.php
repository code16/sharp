<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;

class InstanceCommandController extends ApiController
{
    use HandleCommandReturn;

    /**
     * @param string $entityKey
     * @param string $commandKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     * @throws SharpAuthorizationException
     */
    public function update($entityKey, $commandKey, $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        if(!$list->instanceCommandHandler($commandKey)->authorize()
            || !$list->instanceCommandHandler($commandKey)->authorizeFor($instanceId)) {
            throw new SharpAuthorizationException();
        }


        return $this->returnAsJson(
            $list, $list->instanceCommandHandler($commandKey)->execute($instanceId, (array)request("data"))
        );
    }
}