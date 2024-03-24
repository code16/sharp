<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;
use DOMDocument;
use DOMElement;

class EditorUploadsFormatter extends SharpFieldFormatter implements FormatsAfterUpdate
{
    use HasMaybeLocalizedValue;
    use HandlesHtmlContent;
    
    /**
     * @param SharpFormEditorField $field
     */
    public function toFront(SharpFormField $field, $value)
    {
        if(!$field->uploadsConfig()) {
            return ['text' => $value];
        }
        
        $uploads = [];
        
        $text = $this->maybeLocalized($field, $value, function (string $content) use (&$uploads) {
            $domDocument = $this->parseHtml($content);
            
            foreach ($this->getUploadElements($domDocument) as $element) {
                $file = json_decode($element->getAttribute('file'), true);
                $file = (new SharpUploadModelFormAttributeTransformer())->dynamicInstance()->apply($file);
                $uploads[] = [
                    'file' => $file,
                    'legend' => $element->hasAttribute('legend')
                        ? $element->getAttribute('legend')
                        : null,
                ];
                $element->setAttribute('data-key', count($uploads) - 1);
                $element->removeAttribute('file');
                $element->removeAttribute('legend');
            }
            
            return $this->getHtml($domDocument);
        });
        
        return [
            'text' => $text,
            ...count($uploads) ? [
                'uploads' => $uploads
            ] : [],
        ];
    }
    
    /**
     * @param SharpFormEditorField $field
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if(!$field->uploadsConfig()) {
            return $value['text'] ?? null;
        }
        
        return $this->maybeLocalized(
            $field,
            $value['text'] ?? null,
            function (string $content) use ($field, $value) {
                $domDocument = $this->parseHtml($content);
                
                foreach ($this->getUploadElements($domDocument) as $element) {
                    $key = $element->getAttribute('data-key');
                    $file = $field
                        ->uploadsConfig()
                        ->formatter()
                        ->setInstanceId($this->instanceId)
                        ->setAlwaysReturnFullObject()
                        ->fromFront($field->uploadsConfig(), 'file', $value['uploads'][$key]['file']);
                    $element->setAttribute('file', json_encode($file));
                    if($legend = $value['uploads'][$key]['legend'] ?? null) {
                        $element->setAttribute('legend', $legend);
                    }
                    $element->removeAttribute('data-key');
                }
                
                return $this->getHtml($domDocument);
            }
        );
    }
    
    /**
     * @param SharpFormEditorField $field
     */
    public function afterUpdate(SharpFormField $field, string $attribute, mixed $value): ?string
    {
        if(!$field->uploadsConfig()) {
            return $value;
        }
        
        return $this->maybeLocalized(
            $field,
            $value,
            function (string $content) use ($field) {
                $domDocument = $this->parseHtml($content);
                
                foreach ($this->getUploadElements($domDocument) as $element) {
                    $file = json_decode($element->getAttribute('file'), true);
                    $file = $field
                        ->uploadsConfig()
                        ->formatter()
                        ->setInstanceId($this->instanceId)
                        ->afterUpdate($field->uploadsConfig(), 'file', $file);
                    
                    $element->setAttribute('file', json_encode($file));
                }
                
                return $this->getHtml($domDocument);
            }
        );
    }
    
    /**
     * @return DOMElement[]
     */
    private function getUploadElements(DOMDocument $domDocument): array
    {
        return $this->getRootElementsByTagNames($domDocument, ['x-sharp-image', 'x-sharp-file']);
    }
}
