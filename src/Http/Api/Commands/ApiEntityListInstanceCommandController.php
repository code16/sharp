<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\Http\Api\ApiController;

class ApiEntityListInstanceCommandController extends ApiController
{
    use HandleCommandReturn, HandleCommandForm;

    public function show(string $entityKey, string $commandKey, mixed $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams();

        $commandHandler = $this->getInstanceCommandHandler($list, $commandKey, $instanceId);
        $formData = $commandHandler->formData($instanceId) ?: null;

        return response()->json(
            CommandFormData::from([
                ...$this->getCommandForm($commandHandler),
                'data' => $formData,
                'pageAlert' => $commandHandler->pageAlert($formData),
            ]),
        );
    }

    /**
     * Execute the Command.
     */
    public function update(string $entityKey, string $commandKey, mixed $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams();

        $handler = $this->getInstanceCommandHandler($list, $commandKey, $instanceId);

        return $this->returnCommandResult(
            $list,
            $handler->execute(
                $instanceId,
                $handler->formatRequestData((array) request('data'), $instanceId),
            ),
        );
    }
}
