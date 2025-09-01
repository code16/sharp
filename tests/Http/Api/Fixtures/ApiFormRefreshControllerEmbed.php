<?php

namespace Code16\Sharp\Tests\Http\Api\Fixtures;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;

class ApiFormRefreshControllerEmbed extends SharpFormEditorEmbed
{
    use RefreshFormFields;

    public function buildEmbedConfig(): void
    {
        $this->configureTagName('x-embed');
    }

    public function updateContent(array $data = []): array
    {
        return $data;
    }
}
