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
     */
    public function update($entityKey, $commandKey)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        if(!$list->entityCommandHandler($commandKey)->authorize()) {
            throw new SharpAuthorizationException();
        }

        return $this->returnAsJson(
            $list, $list->entityCommandHandler($commandKey)->execute(
                EntityListQueryParams::create()->fillWithRequest("query"),
                (array)request("data")
            )
        );
    }
}