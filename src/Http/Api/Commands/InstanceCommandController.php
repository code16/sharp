<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Http\Api\ApiController;

class InstanceCommandController extends ApiController
{
    use HandleCommandReturn;

    /**
     * @param string $entityKey
     * @param string $commandKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($entityKey, $commandKey, $instanceId)
    {
        $this->checkAuthorization("update", $entityKey, $instanceId);

        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        return $this->returnAsJson(
            $list, $list->instanceCommandHandler($commandKey)->execute($instanceId)
        );
    }
}