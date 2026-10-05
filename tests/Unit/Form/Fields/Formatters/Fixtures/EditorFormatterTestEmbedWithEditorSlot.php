<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters\Fixtures;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class EditorFormatterTestEmbedWithEditorSlot extends SharpFormEditorEmbed
{
    public function buildEmbedConfig(): void
    {
        $this->configureTagName('x-embed-with-editor-slot');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormEditorField::make('slot')
                ->allowEmbeds([EditorFormatterTestEmbed::class])
        );
    }

    public function updateContent(array $data = []): array
    {
        return $data;
    }
}
