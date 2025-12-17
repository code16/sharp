<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;

class ApiEntityListInstanceCommandController extends ApiController
{
    use HandlesCommandForm;
    use HandlesCommandResult;
    use HandlesInstanceCommand;

    public function __construct(
        private readonly SharpUploadManager $uploadManager,
    ) {
        parent::__construct();
    }

    public function show(string $filterKey, string $entityKey, string $commandKey, mixed $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams(request()->query());

        $commandHandler = $this->getInstanceCommandHandler($list, $commandKey, $instanceId);

        return response()->json(
            CommandFormData::from(
                $this->getCommandForm($commandHandler, $commandHandler->formData($instanceId)),
            ),
        );
    }

    /**
     * Execute the Command.
     */
    public function update(string $filterKey, string $entityKey, string $commandKey, mixed $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams(request()->input('query'));

        $commandHandler = $this->getInstanceCommandHandler($list, $commandKey, $instanceId);

        $formattedData = $commandHandler->formatAndValidateRequestData((array) request('data'), $instanceId);
        $result = $this->returnCommandResult($list, $entityKey, $commandHandler->execute($instanceId, $formattedData));
        $this->uploadManager->dispatchJobs($instanceId);

        return $result;
    }
}
