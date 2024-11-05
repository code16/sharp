<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Intervention\Image\Image;

/**
 * @deprecated Use ThumbnailModifier instead
 */
abstract class ThumbnailFilter
{
    public function __construct(protected array $params) {}

    public function resized()
    {
        return false;
    }

    abstract public function applyFilter(Image $image);
}
