<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;

class ApiEntityListEntityCommandController extends ApiController
{
    use HandlesCommandForm;
    use HandlesCommandReturn;

    public function __construct(
        private readonly SharpUploadManager $uploadManager,
    ) {
        parent::__construct();
    }

    public function show(string $entityKey, string $commandKey)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams(request()->query());

        $commandHandler = $this->getCommandHandler($list, $commandKey);

        return response()->json(
            CommandFormData::from(
                $this->getCommandForm($commandHandler, $commandHandler->formData())
            )
        );
    }

    public function update(string $entityKey, string $commandKey)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams(request()->input('query'));

        $commandHandler = $this->getCommandHandler($list, $commandKey);

        $formattedData = $commandHandler->formatAndValidateRequestData((array) request('data'));
        $result = $this->returnCommandResult($list, $commandHandler->execute($formattedData));
        $this->uploadManager->dispatchJobs();

        return $result;
    }

    protected function getCommandHandler(SharpEntityList $list, string $commandKey): ?EntityCommand
    {
        $commandHandler = $list->findEntityCommandHandler($commandKey);
        $commandHandler->buildCommandConfig();

        if (! $commandHandler->authorize()) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}
