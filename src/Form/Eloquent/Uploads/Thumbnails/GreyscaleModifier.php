<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Intervention\Image\Interfaces\ImageInterface;

class GreyscaleModifier extends ThumbnailModifier
{
    public function apply(ImageInterface $image): ImageInterface
    {
        return $image->greyscale();
    }
}
