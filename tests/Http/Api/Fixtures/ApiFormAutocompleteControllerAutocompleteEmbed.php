<?php

namespace Code16\Sharp\Tests\Http\Api\Fixtures;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormAutocompleteRemoteField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class ApiFormAutocompleteControllerAutocompleteEmbed extends SharpFormEditorEmbed
{
    public function buildEmbedConfig(): void
    {
        $this->configureTagName('x-embed');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormAutocompleteRemoteField::make('autocomplete_field')
                    ->setRemoteMethodPOST()
                    ->setRemoteEndpoint('/my/endpoint')
            );
    }

    public function updateContent(array $data = []): array
    {
        return $data;
    }
}
