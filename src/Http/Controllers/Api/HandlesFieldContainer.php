<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\EntityList\Commands\Command;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Http\Controllers\Api\Commands\HandlesEntityCommand;
use Code16\Sharp\Http\Controllers\Api\Commands\HandlesInstanceCommand;
use Code16\Sharp\Http\Controllers\Api\Embeds\HandlesEmbed;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;

trait HandlesFieldContainer
{
    use HandlesEmbed;
    use HandlesEntityCommand;
    use HandlesInstanceCommand;

    private function getFieldContainer(EntityKey $entityKey): SharpFormEditorEmbed|Command|SharpForm
    {
        if (request()->input('embed_key')) {
            return $this->getEmbedFromKey(request()->input('embed_key'));
        }

        $entity = $this->entityManager->entityFor($entityKey);

        if ($commandKey = request()->input('entity_list_command_key')) {
            if (request()->input('instance_id')) {
                return $this->getInstanceCommandHandler(
                    $entity->getListOrFail(),
                    $commandKey,
                    request()->input('instance_id')
                );
            }

            return $this->getEntityCommandHandler(
                $entity->getListOrFail(),
                $commandKey
            );
        }

        if ($commandKey = request()->input('show_command_key')) {
            return $this->getInstanceCommandHandler(
                $entity->getShowOrFail(),
                $commandKey,
                request()->input('instance_id')
            );
        }

        return $entity->getFormOrFail($entityKey->multiformKey());
    }
}
