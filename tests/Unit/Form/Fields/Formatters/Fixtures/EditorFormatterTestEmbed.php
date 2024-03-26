<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters\Fixtures;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class EditorFormatterTestEmbed extends SharpFormEditorEmbed
{
    public function buildEmbedConfig(): void
    {
        $this->configureTagName('x-embed');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormTextField::make('slot')
        );
    }

    public function updateContent(array $data = []): array
    {
        return $data;
    }
}
