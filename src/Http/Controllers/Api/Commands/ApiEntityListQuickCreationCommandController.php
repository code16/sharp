<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
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
        
        $quickCreationHandler->buildCommandConfig();
        
        $form = $this->entityManager->entityFor($entityKey)->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);
        $form->buildFormConfig();

        $quickCreationHandler
            ->setTitle(__('sharp::breadcrumb.form.create_entity', [
                'entity' => $this->entityManager->entityFor($entityKey)->getLabel()
            ]))
            ->setFormInstance($form);

        return response()->json(
            CommandFormData::from(
                $this->getCommandForm($quickCreationHandler, $quickCreationHandler->formData())
            )
        );
    }

    public function store(string $entityKey)
    {
        $list = $this->getListInstance($entityKey);
        $list->buildListConfig();

        abort_if(
            ($quickCreationHandler = $list->quickCreationCommandHandler()) === null,
            403
        );
        
        $quickCreationHandler->buildCommandConfig();
        
        $form = $this->entityManager->entityFor($entityKey)->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);
        $form->buildFormConfig();

        $quickCreationHandler->setFormInstance($form);

        $formattedData = $quickCreationHandler->formatAndValidateRequestData((array) request('data'));
        $result = $this->returnCommandResult(
            $list,
            $quickCreationHandler->execute($formattedData)
        );
        $this->uploadManager->dispatchJobs();

        return $result;
    }
}
