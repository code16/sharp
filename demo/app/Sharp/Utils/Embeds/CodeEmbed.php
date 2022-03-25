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
            ->configureFormInlineTemplate('<div style="overflow:auto; max-height:100px; font-family:monospace; white-space: pre-wrap">{{ code }}</div>')
            ->configureShowTemplatePath('sharp/templates/codeblock_show_embed.vue');
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
