<?php

namespace Code16\Sharp\Form\Validator;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

abstract class SharpFormRequest extends FormRequest
{
    public function withValidator(Validator $validator): void
    {
        // Find SharpFormEditorField based on their posted structure ($field["text"])
        $editorFields = collect($this->all())
            ->filter(fn ($value) => is_array($value) && array_key_exists('text', $value))
            ->map(function ($value, $key) {
                return is_array($value['text'])
                    ? collect($value['text'])->map(fn ($value, $locale) => "$key.$locale")->toArray()
                    : $key;
            })
            ->flatten()
            ->values()
            ->toArray();

        // Replace Editor rules with .text suffix
        $newRules = collect($validator->getRules())
            ->mapWithKeys(function ($messages, $key) use ($editorFields) {
                if (in_array($key, $editorFields)) {
                    return ["$key.text" => $messages];
                }

                return [$key => $messages];
            })
            ->toArray();

        $validator->setRules($newRules);
    }
}
