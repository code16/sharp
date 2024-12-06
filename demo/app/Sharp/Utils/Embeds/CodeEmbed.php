<?php

namespace App\Sharp\Utils\Embeds;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormTextareaField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class CodeEmbed extends SharpFormEditorEmbed
{
    public function buildEmbedConfig(): void
    {
        $this->configureLabel('Code')
            ->configureTagName('x-codeblock')
            ->configureIcon('fas fa-code')
            ->configureFormTemplate('<div style="overflow:auto; max-height:100px; font-family:monospace; white-space: pre-wrap">{{ $code }}</div>')
            ->configureShowTemplate(view('sharp.templates.codeblock-show-embed'));
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormTextareaField::make('code')
                ->setRowCount(8)
        );
    }

    public function updateContent(array $data = []): array
    {
        $this->validate($data, ['code' => 'required']);

        return $data;
    }
}
