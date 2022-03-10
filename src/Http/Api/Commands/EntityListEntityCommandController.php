<?php

namespace Code16\Sharp\Http\Api\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Api\ApiController;

class EntityListEntityCommandController extends ApiController
{
    use HandleCommandReturn;

    public function show(string $entityKey, string $commandKey)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams();

        $commandHandler = $this->getCommandHandler($list, $commandKey);
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
            'data' => $commandHandler->formData(),
        ]);
    }

    public function update(string $entityKey, string $commandKey)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams();

        $commandHandler = $this->getCommandHandler($list, $commandKey);

        return $this->returnCommandResult(
            $list,
            $commandHandler->execute(
                $commandHandler->formatRequestData((array) request('data')),
            ),
        );
    }

    protected function getCommandHandler(SharpEntityList $list, string $commandKey): ?EntityCommand
    {
        $commandHandler = $list->findEntityCommandHandler($commandKey);
        $commandHandler->buildCommandConfig();
        $commandHandler->initQueryParams(EntityListQueryParams::create()->fillWithRequest('query'));

        if (! $commandHandler->authorize()) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}
