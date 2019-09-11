<?php

namespace Code16\Sharp\Form\Validator;

use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use Illuminate\Validation\Validator;

/**
 * Sharp special implementation of Laravel's Validator to handle specific SharpFormField,
 * like the RTF ones (markdown, wysiwyg) and their ["text"=>...] structure.
 */
class SharpValidator extends Validator
{

    /**
     * @return bool
     */
    public function passes()
    {
        $result = parent::passes();

        // First grab all messages which do not refer to a Rich Text Field (RTF)
        $newMessages = collect($this->messages->getMessages())->filter(function($messages, $key) {
            return !Str::endsWith($key, ".text");
        })->all();

        // Then for all RFT fields, remove the .text in their key (description.text -> description)
        collect($this->messages->getMessages())
            ->filter(function($messages, $key) {
                return Str::endsWith($key, ".text");
            })
            ->each(function($messages, $key) use(&$newMessages) {
                collect($messages)->each(function($message) use($key, &$newMessages) {
                    $newKey = substr($key, 0, -5);
                    $newMessages[$newKey] = str_replace($key, $newKey, $message);
                });
            });

        $this->messages = new MessageBag($newMessages);

        return $result;
    }
}