<?php

namespace Code16\Sharp\Http\Controllers\Api\Embeds;

use Code16\Sharp\Data\Embeds\EmbedFormData;
use Code16\Sharp\Http\Controllers\Controller;
use Code16\Sharp\Utils\Fields\FieldIdentifierFactory;

class ApiEmbedsFormController extends Controller
{
    use HandlesEmbed;

    public function show(FieldIdentifierFactory $fieldIdentifierFactory, string $globalFilter, string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        if ($instanceId) {
            $this->authorizationManager->check('view', $entityKey, $instanceId);
        } else {
            $this->authorizationManager->check('entity', $entityKey);
        }

        $embed = $this->getEmbedFromKey($embedKey);

        return EmbedFormData::from([
            'fields' => $embed->fields(
                $fieldIdentifierFactory
                    ->decrypt(request()->query('identifier'))
                    ->embed(embedKey: $embed->key())
            ),
            'layout' => $embed->formLayout(),
            'data' => $embed->applyFormatters(
                $embed->transformDataForFormFields(request()->all())
            ),
        ]);
    }

    public function update(string $globalFilter, string $embedKey, string $entityKey, ?string $instanceId = null)
    {
        if ($instanceId) {
            $this->authorizationManager->check('update', $entityKey, $instanceId);
        } else {
            $this->authorizationManager->check('create', $entityKey);
        }

        $embed = $this->getEmbedFromKey($embedKey);

        $data = $embed->updateContent(
            $embed->formatRequestData(request()->all())
        );

        return $embed->transformDataWithRenderedTemplate($data, isForm: true);
    }
}
