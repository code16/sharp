<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;

class EditorEmbedsFormatter extends SharpFieldFormatter
{
    use HasMaybeLocalizedValue;
    use HandlesHtmlContent;

    public function toFront(SharpFormField $field, $value)
    {
        $embeds = [];

        $text = $this->maybeLocalized($field, $value, function (string $content) use (&$embeds) {
            $domDocument = $this->parseHtml($content);

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
