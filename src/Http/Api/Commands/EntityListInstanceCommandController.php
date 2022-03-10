<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\Http\Api\ApiController;

class EntityListInstanceCommandController extends ApiController
{
    use HandleCommandReturn;

    public function show(string $entityKey, string $commandKey, mixed $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams();

        $commandHandler = $this->getInstanceCommandHandler($list, $commandKey, $instanceId);
        $formFields = $commandHandler->form();
        $locales = $commandHandler->getDataLocalizations();
        
        return response()->json([
            'form' => count($formFields)
                ? array_merge(
                    [
                        'config' => $commandHandler->commandFormConfig(),
                        'fields' => $formFields,
                        'layout' => $commandHandler->formLayout(),
                    ],
                    $locales ? ["locales" => $locales] : []
                )
                : null,
            'data' => $commandHandler->formData($instanceId),
        ]);
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
