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
        $commandHandler = $list->instanceCommandHandler($commandKey);

        if(!$commandHandler->authorize()
            || !$commandHandler->authorizeFor($instanceId)) {
            throw new SharpAuthorizationException();
        }

        return $this->returnAsJson(
            $list,
            $commandHandler->execute(
                $instanceId,
                $commandHandler->formatRequestData((array)request("data"))
            )
        );
    }
}