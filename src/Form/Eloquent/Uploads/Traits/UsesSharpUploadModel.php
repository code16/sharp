<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Traits;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;

trait UsesSharpUploadModel
{
    /**
     * @return SharpUploadModel
     */
    public static function getUploadModelClass(): string
    {
        return sharp()->config()->get('uploads.model_class') ?: SharpUploadModel::class;
    }
}
