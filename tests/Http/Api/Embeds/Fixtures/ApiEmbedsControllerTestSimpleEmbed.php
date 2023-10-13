<?php

namespace Code16\Sharp\Tests\Http\Api\Embeds\Fixtures;

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class ApiEmbedsControllerTestSimpleEmbed extends SharpFormEditorEmbed
{
    public static string $key = 'Code16.Sharp.Tests.Http.Api.Embeds.Fixtures.ApiEmbedsControllerTestSimpleEmbed';

    public function buildEmbedConfig(): void
    {
        $this->configureTagName('x-test');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
    }

    public function updateContent(array $data = []): array
    {
        return [];
    }
}
