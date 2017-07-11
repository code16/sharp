<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Http\Api\ApiController;

class EntityStateController extends ApiController
{
    use HandleCommandReturn;

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($entityKey, $instanceId)
    {
        $this->checkAuthorization("update", $entityKey, $instanceId);

        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $stateValue = request("value");

        return $this->returnAsJson(
            $list,
            array_merge(
                $list->entityStateHandler()->update($instanceId, $stateValue),
                ["value" => $stateValue]
            )
        );
    }
}