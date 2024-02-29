<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Fakes;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class FakeSharpEditorEmbed extends SharpFormEditorEmbed
{
    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormTextField::make('text')
        );
    }

    public function updateContent(array $data = []): array
    {
    }

    public function buildEmbedConfig(): void
    {
        $this->configureTagName('x-default-fake-sharp-form-editor-embed')
            ->configureLabel('default_fake_sharp_form_editor_embed');
    }
}