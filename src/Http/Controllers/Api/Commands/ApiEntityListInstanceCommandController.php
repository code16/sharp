<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;

class ApiEntityListInstanceCommandController extends ApiController
{
    use HandlesCommandReturn;
    use HandlesCommandForm;

    public function __construct(
        private readonly SharpUploadManager $uploadManager,
    ) {
        parent::__construct();
    }

    public function show(string $entityKey, string $commandKey, mixed $instanceId)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();
        $list->initQueryParams(request()->all());

        $commandHandler = $this->getInstanceCommandHandler($list, $commandKey, $instanceId);
        $formData = $commandHandler->formData($instanceId) ?: null;

        return response()->json(
            CommandFormData::from([
                ...$this->getCommandForm($commandHandler),
                'data' => $commandHandler->applyFormatters($formData),
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
        $list->initQueryParams(request()->input('query'));

        $commandHandler = $this->getInstanceCommandHandler($list, $commandKey, $instanceId);

        $formattedData = $commandHandler->formatAndValidateRequestData((array) request('data'), $instanceId);
        $result = $this->returnCommandResult($list, $commandHandler->execute($instanceId, $formattedData));
        $this->uploadManager->dispatchJobs();

        return $result;
    }
}
