<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Intervention\Image\Interfaces\ModifierInterface;

abstract class ThumbnailModifier implements ModifierInterface
{
    public function resized(): bool
    {
        return false;
    }
}
