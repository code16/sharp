<?php

namespace Code16\Sharp\Form\Validator;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

/**
 * Sharp special implementation of Laravel's Validator to handle specific SharpFormField,
 * like the SharpFormEditorField and their ["text"=>...] structure.
 */
class SharpValidator extends Validator
{
    /**
     * @return bool
     */
    public function passes()
    {
        $result = parent::passes();

        // For all Editor fields, remove the .text in their key (description.text -> description)
        $newMessages = collect($this->messages->getMessages())
            ->mapWithKeys(function ($messages, $key) {
                if(preg_match("/.*[^\\\\].text.*/", $key)) {
                    $newKey = Str::replace(".text", "", $key);
                    return [
                        $newKey => collect($messages)
                            ->map(fn($value) => Str::replace($key, $newKey, $value))
                            ->toArray()
                    ];
                }
                return [
                    $key => $messages
                ];
            })
            ->toArray();

        $this->messages = new MessageBag($newMessages);

        return $result;
    }
}
