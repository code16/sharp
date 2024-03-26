<?php

namespace Code16\Sharp\Utils\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Utils\Fields\IsSharpFieldWithLocalization;
use DOMAttr;
use Illuminate\Support\Str;

trait FormatsEditorEmbedsToFront
{
    protected function formatsEditorEmbedsToFront(IsSharpFieldWithLocalization $field, $value): array
    {
        if (! count($field->embeds())) {
            return ['text' => $value];
        }

        $embeds = [];

        $text = $this->maybeLocalized($field, $value, function (string $content) use (&$embeds, $field) {
            $domDocument = $this->parseHtml($content);

            foreach ($field->embeds() as $embed) {
                $elements = $this->getRootElementsByTagNames($domDocument, [$embed->tagName()]);
                foreach ($elements as $element) {
                    $embeds[$embed->key()][] = $embed->getBuiltFields()
                        ->map(function (SharpFormField $field, $fieldKey) use ($element) {
                            if ($fieldKey === 'slot') {
                                return tap($this->getInnerHtml($element), function () use ($element) {
                                    $this->setInnerHtml($element, '');
                                });
                            }

                            return $element->hasAttribute(Str::kebab($fieldKey))
                                ? $this->tryJsonDecode($element->getAttribute(Str::kebab($fieldKey)))
                                : null;
                        })
                        ->pipe(function ($collection) use ($embed) {
                            return $embed->transformDataForTemplate($collection->toArray(), true);
                        });

                    // remove all attributes as not needed by the front
                    collect($element->attributes)
                        ->each(function (DOMAttr $attribute) use ($element) {
                            $element->removeAttribute($attribute->name);
                        });

                    $element->setAttribute('data-key', count($embeds[$embed->key()]) - 1);
                }
            }

            return $this->getHtml($domDocument);
        });

        return [
            'text' => $text,
            ...count($embeds) ? [
                'embeds' => collect($embeds)
                    ->map(fn ($embeds) => (object) $embeds)
                    ->toArray(),
            ] : [],
        ];
    }

    protected function tryJsonDecode(?string $elementAttributeValue): mixed
    {
        if ($elementAttributeValue === null) {
            return null;
        }

        $decoded = json_decode($elementAttributeValue, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return $elementAttributeValue;
        }

        return $decoded;
    }
}
