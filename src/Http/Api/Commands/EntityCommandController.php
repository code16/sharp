<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;

class EntityCommandController extends ApiController
{
    use HandleCommandReturn;

    /**
     * @param string $entityKey
     * @param string $commandKey
     * @return \Illuminate\Http\JsonResponse
     * @throws SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function update($entityKey, $commandKey)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $commandHandler = $list->entityCommandHandler($commandKey);

        if(! $commandHandler->authorize()) {
            throw new SharpAuthorizationException();
        }

        return $this->returnCommandResult(
            $list,
            $commandHandler->execute(
                EntityListQueryParams::create()->fillWithRequest("query"),
                $commandHandler->formatRequestData((array)request("data"))
            )
        );
    }
}