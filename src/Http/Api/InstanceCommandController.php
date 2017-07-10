<?php

namespace Code16\Sharp\Http\Api;

class InstanceCommandController extends ApiController
{

    /**
     * @param string $entityKey
     * @param string $commandKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($entityKey, $commandKey, $instanceId)
    {
        $this->checkAuthorization("update", $entityKey);

        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        return response()->json(
            $list->instanceCommandHandler($commandKey)->execute($instanceId)
        );
    }
}