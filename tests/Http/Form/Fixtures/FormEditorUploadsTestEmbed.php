<?php

namespace Code16\Sharp\Tests\Http\Form\Fixtures;

use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class FormEditorUploadsTestEmbed extends SharpFormEditorEmbed
{
    public function buildEmbedConfig(): void
    {
        $this->configureTagName('x-embed');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields->addField(
            SharpFormUploadField::make('file')
                ->setStorageBasePath('test/{id}')
                ->setStorageDisk('local')
        );
    }

    public function transformDataForTemplate(array $data, bool $isForm): array
    {
        return $this
            ->setCustomTransformer('file', (new SharpUploadModelFormAttributeTransformer())->dynamicInstance())
            ->transform($data);
    }

    public function updateContent(array $data = []): array
    {
        return $data;
    }
}
