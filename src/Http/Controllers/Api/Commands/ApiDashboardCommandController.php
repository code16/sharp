<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;

class ApiDashboardCommandController extends ApiController
{
    use HandlesCommandForm;
    use HandlesCommandResult;

    public function __construct(
        private readonly SharpUploadManager $uploadManager,
    ) {
        parent::__construct();
    }

    public function show(string $entityKey, string $commandKey)
    {
        $dashboard = $this->getDashboardInstance($entityKey);
        $dashboard->buildDashboardConfig();
        $dashboard->initQueryParams(request()->query());

        $commandHandler = $this->getDashboardCommandHandler($dashboard, $commandKey);
        $formData = $commandHandler->formData() ?: null;

        return response()->json(
            CommandFormData::from(
                $this->getCommandForm($commandHandler, $commandHandler->formData())
            )
        );
    }

    public function update(string $entityKey, string $commandKey)
    {
        $dashboard = $this->getDashboardInstance($entityKey);
        $dashboard->buildDashboardConfig();
        $dashboard->initQueryParams(request()->input('query'));

        $commandHandler = $this->getDashboardCommandHandler($dashboard, $commandKey);

        $formattedData = $commandHandler->formatAndValidateRequestData((array) request('data'));
        $result = $this->returnCommandResult($dashboard, $commandHandler->execute($formattedData));
        $this->uploadManager->dispatchJobs();

        return $result;
    }

    protected function getDashboardCommandHandler(SharpDashboard $dashboard, string $commandKey)
    {
        if ($handler = $dashboard->findDashboardCommandHandler($commandKey)) {
            $handler->buildCommandConfig();

            if (! $handler->authorize()) {
                throw new SharpAuthorizationException();
            }
        }

        return $handler;
    }
}
