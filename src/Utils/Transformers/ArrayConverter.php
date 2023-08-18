<?php

namespace Code16\Sharp\Utils\Transformers;

class ArrayConverter
{
    public static function modelToArray(mixed $model): mixed
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
