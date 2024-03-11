<?php

namespace Code16\Sharp\Utils;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\ThumbnailBuilder;

class Thumbnail
{
    public static function for(?SharpUploadModel $model = null): ThumbnailBuilder
    {
        return new ThumbnailBuilder($model);
    }
}
