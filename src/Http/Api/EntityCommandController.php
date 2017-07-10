<?php

namespace Code16\Sharp\Http\Api;

class EntityCommandController extends ApiController
{

    /**
     * @param string $entityKey
     * @param string $commandKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function update($entityKey, $commandKey)
    {
        $this->checkAuthorization("create", $entityKey);

        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        return $list->entityCommandHandler($commandKey)
            ->execute();
    }
}