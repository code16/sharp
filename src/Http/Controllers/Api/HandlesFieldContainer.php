<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Data\RequestFieldContainerData;
use Code16\Sharp\EntityList\Commands\Command;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Http\Controllers\Api\Commands\HandlesDashboardCommand;
use Code16\Sharp\Http\Controllers\Api\Commands\HandlesEntityCommand;
use Code16\Sharp\Http\Controllers\Api\Commands\HandlesInstanceCommand;
use Code16\Sharp\Http\Controllers\Api\Embeds\HandlesEmbed;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;

trait HandlesFieldContainer
{
    use HandlesDashboardCommand;
    use HandlesEmbed;
    use HandlesEntityCommand;
    use HandlesInstanceCommand;

    private function getFieldContainer(EntityKey $entityKey, bool $isUpdate): SharpFormEditorEmbed|Command|SharpForm
    {
        $requestFieldContainerData = RequestFieldContainerData::from(request()->query());

        if ($requestFieldContainerData->embed_key) {
            $this->authorizationManager->check('entity', $entityKey);

            return $this->getEmbedFromKey($requestFieldContainerData->embed_key);
        }

        $entity = $this->entityManager->entityFor($entityKey);

        if ($commandKey = $requestFieldContainerData->entity_list_command_key) {
            $this->authorizationManager->check('entity', $entityKey);

            if ($requestFieldContainerData->instance_id) {
                return $this->getInstanceCommandHandler(
                    $entity->getListOrFail(),
                    $commandKey,
                    $requestFieldContainerData->instance_id
                );
            }

            return $this->getEntityCommandHandler(
                $entity->getListOrFail(),
                $commandKey
            );
        }

        if ($commandKey = $requestFieldContainerData->show_command_key) {
            $this->authorizationManager->check('view', $entityKey, $requestFieldContainerData->instance_id);

            return $this->getInstanceCommandHandler(
                $entity->getShowOrFail(),
                $commandKey,
                $requestFieldContainerData->instance_id
            );
        }

        if ($commandKey = $requestFieldContainerData->dashboard_command_key) {
            $this->authorizationManager->check('entity', $entityKey);

            return $this->getDashboardCommandHandler(
                $entity->getViewOrFail(),
                $commandKey
            );
        }

        if ($requestFieldContainerData->instance_id !== null || $entity->isSingle()) {
            if ($isUpdate) {
                $this->authorizationManager->check('update', $entityKey, $requestFieldContainerData->instance_id);
            } else {
                $this->authorizationManager->check('view', $entityKey, $requestFieldContainerData->instance_id);
            }
        } else {
            if ($isUpdate) {
                $this->authorizationManager->check('create', $entityKey);
            } else {
                $this->authorizationManager->check('entity', $entityKey);
            }
        }

        return $entity->getFormOrFail();
    }
}
