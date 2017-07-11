<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Http\Api\ApiController;

class EntityCommandController extends ApiController
{
    use HandleCommandReturn;

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

        return $this->returnAsJson(
            $list, $list->entityCommandHandler($commandKey)->execute()
        );
    }
}