<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\Http\Controllers\Api\ApiController;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;
use Illuminate\Support\Str;

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
        $entity = $this->entityManager->entityFor($entityKey);

        $list = $entity->getListOrFail();
        $list->buildListConfig();

        abort_if(
            ($quickCreationHandler = $list->quickCreationCommandHandler()) === null,
            403
        );

        $form = $entity->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);
        $form->buildFormConfig();

        $quickCreationHandler
            ->setEntityKey($entityKey)
            ->setFormInstance($form)
            ->setTitle(__('sharp::breadcrumb.form.create_entity', [
                'entity' => str_contains($entityKey, ':')
                    ? $entity->getMultiforms()[Str::after($entityKey, ':')][1] ?? $entity->getLabel()
                    : $entity->getLabel(),
            ]));

        $quickCreationHandler->buildCommandConfig();

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

        $form = $this->entityManager->entityFor($entityKey)->getFormOrFail(sharp_normalize_entity_key($entityKey)[1]);
        $form->buildFormConfig();

        $quickCreationHandler
            ->setEntityKey($entityKey)
            ->setFormInstance($form);

        $formattedData = $quickCreationHandler->formatAndValidateRequestData((array) request('data'));
        $result = $this->returnCommandResult(
            $list,
            $quickCreationHandler->execute($formattedData)
        );
        $this->uploadManager->dispatchJobs($quickCreationHandler->getInstanceId());

        return $result;
    }
}
