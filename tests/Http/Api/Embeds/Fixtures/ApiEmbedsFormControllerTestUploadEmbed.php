<?php

namespace Code16\Sharp\Tests\Http\Api\Embeds\Fixtures;

use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class ApiEmbedsFormControllerTestUploadEmbed extends SharpFormEditorEmbed
{
    public static string $key = 'Code16.Sharp.Tests.Http.Api.Embeds.Fixtures.ApiEmbedsFormControllerTestUploadEmbed';

    public function buildEmbedConfig(): void
    {
        $this->configureTagName('x-upload-embed');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(SharpFormUploadField::make('visual'))
            ->addField(
                SharpFormListField::make('pictures')
                    ->addItemField(SharpFormUploadField::make('file'))
            )
            ->addField(
                SharpFormEditorField::make('slot')
                    ->allowUploads(SharpFormEditorUpload::make())
            );
    }

    public function transformDataForTemplate(array $data, bool $isForm): array
    {
        return $this
            ->setCustomTransformer('visual', (new SharpUploadModelFormAttributeTransformer())->dynamicInstance())
            ->transform($data);
    }

    public function transformDataForFormFields(array $data): array
    {
        return $this
            ->setCustomTransformer('visual', (new SharpUploadModelFormAttributeTransformer())->dynamicInstance())
            ->transform($data);
    }

    public function updateContent(array $data = []): array
    {
        return $data;
    }
}
