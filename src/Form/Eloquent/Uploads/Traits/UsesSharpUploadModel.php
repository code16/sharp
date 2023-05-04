<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Traits;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;

trait UsesSharpUploadModel
{
    public static function getUploadModelClass(): string
    {
        return config('sharp.uploads.model_class') ?: SharpUploadModel::class;
    }
}
