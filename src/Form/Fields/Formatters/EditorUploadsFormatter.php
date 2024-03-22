<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;
use DOMDocument;
use DOMElement;

class EditorUploadsFormatter extends SharpFieldFormatter
{
    use HasMaybeLocalizedValue;
    use HandlesHtmlContent;
    
    public function toFront(SharpFormField $field, $value)
    {
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
                $element->setAttribute('id', count($uploads) - 1);
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
        return $this->maybeLocalized(
            $field,
            $value['text'] ?? null,
            function (string $content) use ($field, $value) {
                $domDocument = $this->parseHtml($content);
                
                foreach ($this->getUploadElements($domDocument) as $element) {
                    $id = $element->getAttribute('id');
                    $file = $value['uploads'][$id]['file'];
                    $formatted = $field
                        ->uploadsConfig()
                        ->formatter()
                        ->setInstanceId($this->instanceId)
                        ->setAlwaysReturnFullObject()
                        ->fromFront($field->uploadsConfig(), 'file', $file);
                    
                    $formatted = collect($formatted)->whereNotNull()->toArray();
                    
                    $element->setAttribute('file', json_encode($formatted));
                    
                    if($legend = $value['uploads'][$id]['legend'] ?? null) {
                        $element->setAttribute('legend', $legend);
                    }
                    
                    $element->removeAttribute('id');
                }
                
                return $this->getHtml($domDocument);
            }
        );
    }
    
    /**
     * @param SharpFormEditorField $field
     */
    public function afterUpdate(SharpFormField $field, string $attribute, $value)
    {
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
