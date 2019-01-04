<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;

class InstanceCommandController extends ApiController
{
    use HandleCommandReturn;

    /**
     * Display the Command form.
     *
     * @param string $entityKey
     * @param string $commandKey
     * @param $instanceId
     * @return \Illuminate\Http\JsonResponse
     * @throws SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function show($entityKey, $commandKey, $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $commandHandler = $this->getCommandHandler($list, $commandKey, $instanceId);

        return response()->json([
            "data" => $commandHandler->formData($instanceId)
        ]);
    }

    /**
     * Execute the Command.
     *
     * @param string $entityKey
     * @param string $commandKey
     * @param string $instanceId
     * @return \Illuminate\Http\JsonResponse
     * @throws SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function update($entityKey, $commandKey, $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        $handler = $this->getCommandHandler($list, $commandKey, $instanceId);

        return $this->returnCommandResult(
            $list,
            $handler->execute(
                $instanceId,
                $handler->formatRequestData((array)request("data"), $instanceId)
            )
        );
    }

    /**
     * @param SharpEntityList $list
     * @param string $commandKey
     * @param $instanceId
     * @return \Code16\Sharp\EntityList\Commands\InstanceCommand|null
     * @throws SharpAuthorizationException
     */
    protected function getCommandHandler(SharpEntityList $list, $commandKey, $instanceId)
    {
        $commandHandler = $list->instanceCommandHandler($commandKey);

        if(!$commandHandler->authorize()
            || !$commandHandler->authorizeFor($instanceId)) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}