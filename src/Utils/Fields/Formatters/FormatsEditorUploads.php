<?php

namespace Code16\Sharp\Utils\Fields\Formatters;

use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithEmbeds;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithLocalization;
use DOMDocument;
use DOMElement;

trait FormatsEditorUploads
{
    use FormatsHtmlContent;
    
    protected function formatEditorUploadsToFront(IsSharpFieldWithEmbeds&IsSharpFieldWithLocalization $field, $value): array
    {
        if(!str_contains($value, '<x-sharp-image') && !str_contains($value, '<x-sharp-file')) {
            return [
                'text' => $value,
            ];
        }
        
        $uploads = [];

        $text = $this->maybeLocalized($field, $value, function (string $content) use (&$uploads) {
            $domDocument = $this->parseHtml($content);

            foreach ($this->getUploadElements($domDocument) as $element) {
                $file = json_decode($element->getAttribute('file'), true);
                $file = (new SharpUploadModelFormAttributeTransformer(
                    withThumbnails: $element->tagName === 'x-sharp-image'
                ))->dynamicInstance()->apply($file);

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

            return $this->toHtml($domDocument);
        });

        return [
            'text' => $text,
            ...count($uploads) ? [
                'uploads' => (object) $uploads,
            ] : [],
        ];
    }

    /**
     * @return DOMElement[]
     */
    protected function getUploadElements(DOMDocument $domDocument): array
    {
        return $this->getRootElementsByTagNames($domDocument, ['x-sharp-image', 'x-sharp-file']);
    }
}
