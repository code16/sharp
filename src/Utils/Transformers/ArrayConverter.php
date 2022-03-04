<?php

namespace Code16\Sharp\Utils\Transformers;

class ArrayConverter
{
    /**
     * Convert a class or Model into an array.
     * If $model is not an array or model, simply return it.
     *
     * @param array|object|mixed $model
     *
     * @return array|mixed
     */
    public static function modelToArray($model)
    {
        if (is_array($model)) {
            return $model;
        }

        if (is_object($model)) {
            if (method_exists($model, 'toArray')) {
                return $model->toArray();
            }

            return (array) $model;
        }

        // Model is not a structured data. We return it as it is.
        return $model;
    }
}
