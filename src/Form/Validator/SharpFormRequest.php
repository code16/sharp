<?php

namespace Code16\Sharp\Form\Validator;

use Code16\Sharp\Http\WithSharpContext;
use Illuminate\Foundation\Http\FormRequest;

abstract class SharpFormRequest extends FormRequest
{
    use WithSharpContext;

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Handle RTF (markdown and wysiwyg) fields
     *
     * @param  Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        // Find RTF (markdown, wysiwyg) based on their posted structure ($field["text"])
        $richTextFields = collect($this->all())->filter(function($value, $key) {
            return is_array($value) && array_key_exists("text", $value);
        })->keys()->all();

        // Initialize rules by getting all those which DO NOT refer to a RTF
        $newRules = collect($validator->getRules())
            ->filter(function($messages, $key) use($richTextFields) {
                return !in_array($key, $richTextFields);
            })
            ->all();

        // And then replace RTF rules with .text suffix
        collect($validator->getRules())->filter(function($messages, $key) use($richTextFields) {
            return in_array($key, $richTextFields);
        })->each(function($messages, $key) use(&$newRules) {
            $newRules["$key.text"] = $messages;
        });

        $validator->setRules($newRules);
    }
}