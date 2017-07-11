<?php

namespace Code16\Sharp\Form\Eloquent;

use Closure;
use Code16\Sharp\Form\Eloquent\Transformers\EloquentFormTagsTransformer;

trait WithFormEloquentTransformers
{

    /**
     * @param string $attribute
     * @param string|Closure $labelAttribute
     * @param string $idAttribute
     * @return $this
     */
    function setTagsTransformer(string $attribute, $labelAttribute, $idAttribute = "id")
    {
        $this->transformers[$attribute] = new EloquentFormTagsTransformer(
            $labelAttribute, $idAttribute
        );

        return $this;
    }
}