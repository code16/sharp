<?php

namespace Code16\Sharp\EntityList\Eloquent;

use Code16\Sharp\EntityList\Eloquent\Transformers\EloquentEntityListUploadTransformer;

trait WithEntityListEloquentTransformers
{

    /**
     * @param string $attribute
     * @param int|null $width
     * @param int|null $height
     * @param array $filters
     * @return $this
     */
    function setUploadTransformer(string $attribute, int $width = null, int $height = null, array $filters = [])
    {
        $this->transformers[$attribute] = new EloquentEntityListUploadTransformer(
            $attribute, $width, $height, $filters
        );

        return $this;
    }
}