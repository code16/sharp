<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\QuickCreate\QuickCreationCommand;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;

class ApiEntityListQuickCreationCommandController extends ApiController
{
    use HandlesCommandForm;
    use HandlesCommandResult;

    public function __construct(private readonly SharpUploadManager $uploadManager)
    {
        parent::__construct();
    }

    public function create(string $entityKey)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        abort_if(
            ($quickCreationHandler = $list->quickCreationCommandHandler()) === null,
            403
        );

        $quickCreationHandler->setFormInstance(
            $this->entityManager->entityFor($entityKey)->getFormOrFail()
        );

        return response()->json(
            CommandFormData::from(
                $this->getCommandForm($quickCreationHandler, $quickCreationHandler->formData())
            )
        );
    }

    public function store(string $entityKey)
    {
        //        $list = $this->getListInstance($entityKey);
        //        $list->buildListConfig();
        //        $list->initQueryParams(request()->input('query'));
        //
        //        $commandHandler = $this->getEntityCommandHandler($list, $commandKey);
        //
        //        $formattedData = $commandHandler->formatAndValidateRequestData((array) request('data'));
        //        $result = $this->returnCommandResult($list, $commandHandler->execute($formattedData));
        //        $this->uploadManager->dispatchJobs();
        //
        //        return $result;
    }

    private function getEntityCommandHandler(): EntityCommand
    {
        $commandHandler = app(QuickCreationCommand::class);
        $commandHandler->buildCommandConfig();

        return $commandHandler;
    }
}
