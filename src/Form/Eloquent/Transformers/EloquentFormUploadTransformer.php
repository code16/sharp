<?php

namespace Code16\Sharp\Form\Eloquent\Transformers;

use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;

class EloquentFormUploadTransformer implements SharpAttributeTransformer
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
        $upload = $instance->$attribute;

        return $upload && $upload->file_name
            ? [
                "name" => $upload->file_name,
                "thumbnail" => $upload->thumbnail(null, 150),
                "size" => $upload->size,
            ]
            : null;
    }
}