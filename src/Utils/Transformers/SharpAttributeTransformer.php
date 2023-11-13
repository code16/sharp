<?php

namespace Code16\Sharp\Utils\Transformers;

interface SharpAttributeTransformer
{
    /**
     * Transform a model attribute to array (json-able).
     *
     * @param  $value
     * @param  $instance
     * @param  string  $attribute
     * @return mixed
     */
    public function apply($value, $instance = null, $attribute = null);
}
