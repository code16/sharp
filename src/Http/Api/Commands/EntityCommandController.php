<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;

class EntityCommandController extends ApiController
{
    use HandleCommandReturn;

    /**
     * @param string $entityKey
     * @param string $commandKey
     * @return \Illuminate\Http\JsonResponse
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function show($entityKey, $commandKey)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $commandHandler = $this->getCommandHandler($list, $commandKey);

        return response()->json([
            "data" => $commandHandler->formData()
        ]);
    }

    /**
     * @param string $entityKey
     * @param string $commandKey
     * @return \Illuminate\Http\JsonResponse
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function update($entityKey, $commandKey)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $commandHandler = $this->getCommandHandler($list, $commandKey);

        return $this->returnCommandResult(
            $list,
            $commandHandler->execute(
                EntityListQueryParams::create()->fillWithRequest("query"),
                $commandHandler->formatRequestData((array)request("data"))
            )
        );
    }

    /**
     * @param SharpEntityList $list
     * @param string $commandKey
     * @return \Code16\Sharp\EntityList\Commands\EntityCommand|null
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     */
    protected function getCommandHandler(SharpEntityList $list, $commandKey)
    {
        $commandHandler = $list->entityCommandHandler($commandKey);

        if(! $commandHandler->authorize()) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}