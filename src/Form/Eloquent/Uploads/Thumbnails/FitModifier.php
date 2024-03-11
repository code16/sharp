<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Intervention\Image\Interfaces\ImageInterface;

class FitModifier extends ThumbnailModifier
{
    public function __construct(private readonly int $width, private readonly int $height)
    {
    }

    public function apply(ImageInterface $image): ImageInterface
    {
        return $image->cover($this->width, $this->height);
    }

    public function resized(): bool
    {
        return true;
    }
}
