<?php

namespace App\Sharp\Embeds;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TestEmbed extends SharpFormEditorEmbed
{
    public function buildEmbedConfig(): void
    {
        $this->configureLabel('Test embed')
            ->configureTagName('x-test-embed')
            ->configureIcon('icon-flask')
            ->configureTemplate('Test embed: {{ $title }}');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('title')
                    ->setLabel('Title')
            );
    }

    public function updateContent(array $data = []): array
    {
        $this->validate($data, [
            'title' => 'required'
        ]);

        return $data;
    }
}
