<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;

class EditorFormatter extends SharpFieldFormatter
{
    public function toFront(SharpFormField $field, $value)
    {
        return [
            'text' => $value,
        ];
    }

    /**
     * @param  SharpFormEditorField  $field
     */
    public function fromFront(SharpFormField $field, string $attribute, $value)
    {
        $content = $value['text'] ?? '';

        if ($value !== null && $field->isLocalized()) {
            return collect(is_array($content) ? $content : [app()->getLocale() => $content])
                ->union(collect($this->dataLocalizations ?? [])->mapWithKeys(fn ($locale) => [$locale => null]))
                ->map(function (?string $localizedContent) {
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

    /**
     * @param  SharpFormEditorField  $field
     */
    public function afterUpdate(SharpFormField $field, $attribute, $value): string|array|null
    {
        if ($value !== null && $field->isLocalized()) {
            return collect($value)
                ->map(function (?string $localizedContent) {
                    return $localizedContent
                        ? str($localizedContent)->replace(UploadFormatter::ID_PLACEHOLDER, $this->instanceId)->value()
                        : null;
                })
                ->toArray();
        }

        return $value
            ? str($value)->replace(UploadFormatter::ID_PLACEHOLDER, $this->instanceId)->value()
            : null;
    }
}
