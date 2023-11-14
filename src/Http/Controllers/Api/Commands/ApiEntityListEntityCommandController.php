<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\EntityListQueryParams;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Http\Controllers\HandlesUploadedFilesInRequest;

class ApiEntityListEntityCommandController extends ApiController
{
    use HandlesCommandReturn;
    use HandlesCommandForm;
    use HandlesUploadedFilesInRequest;

    public function show(string $entityKey, string $commandKey)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams();

        $commandHandler = $this->getCommandHandler($list, $commandKey);
        $formData = $commandHandler->formData() ?: null;

        return response()->json(
            CommandFormData::from([
                ...$this->getCommandForm($commandHandler),
                'data' => $formData,
                'pageAlert' => $commandHandler->pageAlert($formData),
            ])
        );
    }

    public function update(string $entityKey, string $commandKey)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams();

        $commandHandler = $this->getCommandHandler($list, $commandKey);

        $formattedData = $commandHandler->formatAndValidateRequestData((array) request('data'));
        $result = $this->returnCommandResult($list, $commandHandler->execute($formattedData));
        $this->handlePostedFiles($commandHandler, request()->all(), $formattedData);

        return $result;
    }

    protected function getCommandHandler(SharpEntityList $list, string $commandKey): ?EntityCommand
    {
        $commandHandler = $list->findEntityCommandHandler($commandKey);
        $commandHandler->buildCommandConfig();
        $commandHandler->initQueryParams(EntityListQueryParams::create()->fillWithRequest());

        if (! $commandHandler->authorize()) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}
