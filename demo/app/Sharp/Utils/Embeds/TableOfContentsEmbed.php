<?php

namespace App\Sharp\Utils\Embeds;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class TableOfContentsEmbed extends SharpFormEditorEmbed
{
    public function buildEmbedConfig(): void
    {
        $this->configureLabel('Table of contents')
            ->configureTagName('x-table-of-contents');
    }

    public function buildFormFields(FieldsContainer $formFields): void {}

    public function updateContent(array $data = []): array
    {
        return [];
    }
}
