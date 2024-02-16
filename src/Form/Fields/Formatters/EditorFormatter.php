<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use DOMDocument;
use DOMElement;
use Illuminate\Support\Str;

class EditorFormatter extends SharpFieldFormatter
{
    public function toFront(SharpFormField $field, $value)
    {
        return [
            'text' => $value,
        ];
    }

    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        $content = $value['text'] ?? '';

        if ($value !== null && $field->isLocalized()) {
            return collect(is_array($content) ? $content : [app()->getLocale() => $content])
                ->union(collect($this->dataLocalizations ?? [])->mapWithKeys(fn ($locale) => [$locale => null]))
                ->map(function (?string $localizedContent) use ($field, $attribute) {
                    return $localizedContent
                        ? preg_replace(
                            '/\R/', "\n",
                            $localizedContent,
                        )
                        : null;
                })
                ->toArray();
        }

        return preg_replace('/\R/u', "\n", $content);
    }

    public function afterUpdate(SharpFormField $field, $attribute, $value): ?string
    {
        return $value
            ? str($value)->replace(UploadFormatter::ID_PLACEHOLDER, $this->instanceId)->value()
            : null;
    }
}
