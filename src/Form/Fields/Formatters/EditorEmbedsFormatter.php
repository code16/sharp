<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;
use DOMNode;
use Illuminate\Support\Str;

class EditorEmbedsFormatter extends SharpFieldFormatter
{
    use HasMaybeLocalizedValue;
    use HandlesHtmlContent;
    
    /**
     * @param SharpFormEditorField $field
     */
    public function toFront(SharpFormField $field, $value)
    {
        $embeds = [];

        $text = $this->maybeLocalized($field, $value, function (string $content) use (&$embeds, $field) {
            $domDocument = $this->parseHtml($content);
            
            foreach ($field->embeds() as $embed) {
                $elements = $this->getRootElementsByTagNames($domDocument, [$embed->tagName()]);
                foreach ($elements as $element) {
                    $embeds[$embed->key()][] = collect($element->attributes)
                        ->mapWithKeys(function (DOMNode $attribute) use ($element) {
                            
                            $element->removeAttribute($attribute->nodeName);
                        });
                }
            }

            return $this->getHtml($domDocument);
        });

        return [
            'text' => $text,
            ...count($embeds) ? [
                'embeds' => $embeds,
            ] : [],
        ];
    }

    /**
     * @param  SharpFormEditorField  $field
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        return $this->maybeLocalized(
            $field,
            $value['text'] ?? null,
            function (string $content) {
                $domDocument = $this->parseHtml($content);

                return $this->getHtml($domDocument);
            }
        );
    }

    /**
     * @param  SharpFormEditorField  $field
     */
    public function afterUpdate(SharpFormField $field, string $attribute, $value)
    {
        return $this->maybeLocalized(
            $field,
            $value,
            function (string $content) {
                $domDocument = $this->parseHtml($content);

                return $this->getHtml($domDocument);
            }
        );
    }
}
