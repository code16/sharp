<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters\Fixtures;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class EditorFormatterTestEmbedWithNestedEditorSlot extends SharpFormEditorEmbed
{
    public function buildEmbedConfig(): void
    {
        $this->configureTagName('x-embed-with-nested-editor-slot');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormEditorField::make('slot')
                ->allowEmbeds([EditorFormatterTestEmbedWithEditorSlot::class, self::class])
        );
    }

    public function updateContent(array $data = []): array
    {
        return $data;
    }
}
