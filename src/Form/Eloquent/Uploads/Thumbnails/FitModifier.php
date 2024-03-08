<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Intervention\Image\Interfaces\ImageInterface;

class FitModifier extends ThumbnailModifier
{
    public function apply(ImageInterface $image): ImageInterface
    {
        return $image->cover($this->params['w'], $this->params['h']);
    }

    public function resized(): bool
    {
        return true;
    }
}
