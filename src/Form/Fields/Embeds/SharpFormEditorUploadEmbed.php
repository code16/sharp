<?php

namespace Code16\Sharp\Form\Fields\Embeds;

use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;
use Code16\Sharp\Utils\Fields\FieldsContainer;

class SharpFormEditorUploadEmbed extends SharpFormEditorEmbed
{
    use SharpFormFieldWithUpload;
    
    public function buildEmbedConfig(): void
    {
        $this->configureLabel(__('sharp::form.editor.toolbar.upload.title'))
            ->configureTagName('')
            ->configureFormInlineTemplate('');
    }
    
    public function buildFormFields(FieldsContainer $formFields): void
    {
        $formFields
            ->addField($this->getUploadField());
    }
    
    protected function getUploadField()
    {
        $field = SharpFormUploadField::make('file');
        
        if(isset($this->payload['maxFileSize'])) {
            $field->setMaxFileSize($this->payload['maxFileSize']);
        }
        
        if(isset($this->payload['ratioX'])) {
            $field->setCropRatio($this->payload['ratioX'].':'.$this->payload['ratioY'], $this->payload['transformableFileTypes']);
        }
        
        return $field
            ->setFileFilter($this->payload['fileFilter'])
            ->setTransformable($this->payload['transformable'], $this->payload['transformKeepOriginal']);
    }
    
    public function transformDataForTemplate(array $data, bool $isForm): array
    {
        return $this
            ->setCustomTransformer('file', (new SharpUploadModelFormAttributeTransformer())->dynamicInstance())
            ->transformForTemplate($data);
    }
    
    public function transformDataForFormFields(array $data): array
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
