<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;

class EntityListInstanceCommandController extends ApiController
{
    use HandleCommandReturn;

    /**
     * Display the Command form.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function show(string $entityKey, string $commandKey, mixed $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->init();
        
        $commandHandler = $this->getInstanceCommandHandler($list, $commandKey, $instanceId);

        return response()->json([
            "data" => $commandHandler->formData($instanceId)
        ]);
    }

    /**
     * Execute the Command.
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function update(string $entityKey, string $commandKey, mixed $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->init();

        $handler = $this->getInstanceCommandHandler($list, $commandKey, $instanceId);

        return $this->returnCommandResult(
            $list,
            $handler->execute(
                $instanceId,
                $handler->formatRequestData((array)request("data"), $instanceId)
            )
        );
    }
}