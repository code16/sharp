<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Show\SharpSingleShow;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;

class ApiShowInstanceCommandController extends ApiController
{
    use HandlesCommandReturn;
    use HandlesCommandForm;

    public function __construct(
        private readonly SharpUploadManager $uploadManager,
    ) {
        parent::__construct();
    }

    public function show(string $entityKey, string $commandKey, mixed $instanceId = null)
    {
        $showPage = $this->getShowPage($entityKey, $instanceId);
        $commandHandler = $this->getInstanceCommandHandler($showPage, $commandKey, $instanceId);

        return response()->json(
            CommandFormData::from([
                ...$this->getCommandForm($commandHandler),
                'data' => $commandHandler->applyFormatters($commandHandler->formData($instanceId) ?: null),
                'pageAlert' => $commandHandler->pageAlert($commandHandler->allFormData($instanceId)),
            ]),
        );
    }

    public function update(string $entityKey, string $commandKey, mixed $instanceId = null)
    {
        $showPage = $this->getShowPage($entityKey, $instanceId);
        $commandHandler = $this->getInstanceCommandHandler($showPage, $commandKey, $instanceId);

        $formattedData = $commandHandler->formatAndValidateRequestData((array) request('data'), $instanceId);
        $result = $this->returnCommandResult($showPage, $commandHandler->execute($formattedData));
        $this->uploadManager->dispatchJobs($instanceId);

        return $result;
    }

    private function getShowPage(string $entityKey, mixed $instanceId = null)
    {
        $showPage = $this->getShowInstance($entityKey);

        abort_if(
            (! $instanceId && ! $showPage instanceof SharpSingleShow)
            || ($instanceId && $showPage instanceof SharpSingleShow),
            404,
        );

        $showPage->buildShowConfig();

        return $showPage;
    }
}
