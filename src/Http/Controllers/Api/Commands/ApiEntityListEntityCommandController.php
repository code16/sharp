<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\Http\Controllers\Controller;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;

class ApiEntityListEntityCommandController extends Controller
{
    use HandlesCommandForm;
    use HandlesCommandResult;
    use HandlesEntityCommand;

    public function __construct(
        private readonly SharpUploadManager $uploadManager,
    ) {
        parent::__construct();
    }

    public function show(string $globalFilter, string $entityKey, string $commandKey)
    {
        $this->authorizationManager->check('entity', $entityKey);

        $list = $this->entityManager->entityFor($entityKey)->getListOrFail();
        $list->buildListConfig();
        $list->initQueryParams(request()->query());

        $commandHandler = $this->getEntityCommandHandler($list, $commandKey);

        return response()->json(
            CommandFormData::from(
                $this->getCommandForm($commandHandler, $commandHandler->formData())
            )
        );
    }

    public function update(string $globalFilter, string $entityKey, string $commandKey)
    {
        $this->authorizationManager->check('entity', $entityKey);

        $list = $this->entityManager->entityFor($entityKey)->getListOrFail();
        $list->buildListConfig();
        $list->initQueryParams(request()->input('query'));

        $commandHandler = $this->getEntityCommandHandler($list, $commandKey);

        $formattedData = $commandHandler->formatAndValidateRequestData((array) request('data'));
        $result = $this->returnCommandResult($list, $entityKey, $commandHandler->execute($formattedData));
        $this->uploadManager->dispatchJobs();

        return $result;
    }
}
