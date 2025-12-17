<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;

class ApiEntityListQuickCreationCommandController extends ApiController
{
    use HandlesCommandForm;
    use HandlesCommandResult;

    public function __construct(private readonly SharpUploadManager $uploadManager)
    {
        parent::__construct();
    }

    public function create(string $filterKey, EntityKey $entityKey, EntityKey $formEntityKey)
    {
        $entity = $this->entityManager->entityFor($entityKey);

        $list = $entity->getListOrFail();
        $list->buildListConfig();

        abort_if(
            ($quickCreationHandler = $list->quickCreationCommandHandler()) === null,
            403
        );

        $form = $this->entityManager->entityFor($formEntityKey)->getFormOrFail($formEntityKey->multiformKey());
        $form->buildFormConfig();

        $quickCreationHandler
            ->setEntityKey($entityKey)
            ->setFormInstance($form)
            ->setTitle(__('sharp::breadcrumb.form.create_entity', [
                'entity' => $entity->getLabelOrFail($entityKey->multiformKey()),
            ]));

        $quickCreationHandler->buildCommandConfig();

        return response()->json(
            CommandFormData::from(
                $this->getCommandForm($quickCreationHandler, $quickCreationHandler->formData())
            )
        );
    }

    public function store(string $filterKey, EntityKey $entityKey, EntityKey $formEntityKey)
    {
        $list = $this->entityManager->entityFor($entityKey)->getListOrFail();
        $list->buildListConfig();

        abort_if(
            ($quickCreationHandler = $list->quickCreationCommandHandler()) === null,
            403
        );

        $form = $this->entityManager->entityFor($formEntityKey)->getFormOrFail($formEntityKey->multiformKey());
        $form->buildFormConfig();

        $quickCreationHandler
            ->setEntityKey($entityKey)
            ->setFormInstance($form);

        $formattedData = $quickCreationHandler->formatAndValidateRequestData((array) request('data'));
        $result = $this->returnCommandResult(
            $list,
            $entityKey,
            $quickCreationHandler->execute($formattedData)
        );
        $this->uploadManager->dispatchJobs($quickCreationHandler->getInstanceId());

        return $result;
    }
}
