<?php

namespace App\Sharp\Utils\Embeds;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TextEmbed extends SharpFormEditorEmbed
{
    public function key(): string
    {
        return 'text';
    }

    public function buildEmbedConfig(): void
    {
        $this->configureLabel('Text')
            ->configureTagName('x-text')
            ->configureInlineFormTemplate('{{ content }} ({{ status }})');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormTextField::make('content')
                ->setLabel('Content'),
        );
    }
}