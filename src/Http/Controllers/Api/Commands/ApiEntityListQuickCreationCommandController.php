<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Data\Commands\CommandFormData;
use Code16\Sharp\EntityList\EntityListEntity;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Http\Controllers\Controller;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;
use Code16\Sharp\Utils\Uploads\SharpUploadManager;

class ApiEntityListQuickCreationCommandController extends Controller
{
    use HandlesCommandForm;
    use HandlesCommandResult;

    public function __construct(private readonly SharpUploadManager $uploadManager)
    {
        parent::__construct();
    }

    public function create(string $globalFilter, EntityKey $entityKey, EntityKey $formEntityKey)
    {
        $this->authorizationManager->check('create', $entityKey);
        $entity = $this->entityManager->entityFor($entityKey);

        $list = $entity->getListOrFail();
        $list->buildListConfig();

        abort_if(
            ($quickCreationHandler = $list->quickCreationCommandHandler()) === null,
            403
        );

        $form = $this->getQuickCreationFormOrFail($list, $entityKey, $formEntityKey);

        $quickCreationHandler
            ->setEntityKey($entityKey)
            ->setFormInstance($form)
            ->setTitle(__('sharp::breadcrumb.form.create_entity', [
                'entity' => $entity->getLabelOrFail(),
            ]));

        $quickCreationHandler->buildCommandConfig();

        return response()->json(
            CommandFormData::from(
                $this->getCommandForm($quickCreationHandler, $quickCreationHandler->formData())
            )
        );
    }

    public function store(string $globalFilter, EntityKey $entityKey, EntityKey $formEntityKey)
    {
        $this->authorizationManager->check('create', $entityKey);
        $list = $this->entityManager->entityFor($entityKey)->getListOrFail();
        $list->buildListConfig();

        abort_if(
            ($quickCreationHandler = $list->quickCreationCommandHandler()) === null,
            403
        );

        $form = $this->getQuickCreationFormOrFail($list, $entityKey, $formEntityKey);

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

    /**
     * The form must be the one of the list entity, or of one of its sub-entities,
     * and the user must be allowed to create an instance of it.
     */
    private function getQuickCreationFormOrFail(SharpEntityList $list, EntityKey $entityKey, EntityKey $formEntityKey): SharpForm
    {
        abort_unless(
            $formEntityKey->toString() === $entityKey->toString()
            || collect($list->getEntities()?->all() ?? [])
                ->contains(fn (EntityListEntity $listEntity) => $listEntity->getEntityKey() === $formEntityKey->toString()),
            403
        );

        $this->authorizationManager->check('create', $formEntityKey);

        $form = $this->entityManager->entityFor($formEntityKey)->getFormOrFail();
        $form->buildFormConfig();

        return $form;
    }
}
