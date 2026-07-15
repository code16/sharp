<?php

namespace Code16\Sharp\Http\Controllers\Api\Embeds;

use Code16\Sharp\Data\RequestFieldContainerData;
use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Utils\Entities\ValueObjects\EntityKey;

trait HandlesEmbed
{
    protected function getEmbedFromKey(
        string $entityKey,
        string $embedKey,
        ?RequestFieldContainerData $requestFieldContainerData = null,
    ): SharpFormEditorEmbed {
        $requestFieldContainerData ??= RequestFieldContainerData::from(request()->query());
        $requestFieldContainerData->embed_key = null; // remove embed key to prevent infinite loop
        $container = $this->getFieldContainer(new EntityKey($entityKey), $requestFieldContainerData);
        /** @var SharpFormEditorField $editorField */
        $editorField = $container->findFieldByKey($requestFieldContainerData->embed_editor_key);
        $embed = $editorField->embeds()->first(fn (SharpFormEditorEmbed $embed) => $embed->key() === $embedKey);

        if (! $embed) {
            throw new SharpException('Embed not found');
        }

        return $embed;
    }
}
