<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters\Fixtures;

use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbed;
use Code16\Sharp\Form\Fields\SharpFormTextField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class EditorFormatterTestEmbed extends SharpFormEditorEmbed
{
    public function buildEmbedConfig(): void
    {
        $this->configureTagName('x-embed')
            ->configureIcon('testicon-car');
    }

    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField(
                SharpFormTextField::make('slot')
            )->addField(
                SharpFormUploadField::make('visual')->setImageOnly()
            );
    }

    public function transformDataForTemplate(array $data, bool $isForm): array
    {
        return $this->setCustomTransformer('visual', (new SharpUploadModelFormAttributeTransformer())->dynamicInstance())
            ->transformForTemplate($data);
    }

    public function updateContent(array $data = []): array
    {
        return $data;
    }
}
