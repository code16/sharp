<?php

namespace Code16\Sharp\Form\Transformers;

use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;

/**
 * Handles the transformation of a string value to a valid form
 * expected by the Markdown front field.
 */
class FormMarkdownTransformer implements SharpAttributeTransformer
{

    /**
     * Transform a model attribute to array (json-able).
     *
     * @param $instance
     * @param string $attribute
     * @return mixed
     */
    function apply($instance, string $attribute)
    {
        return [
            "text" => $instance->$attribute
        ];
    }
}