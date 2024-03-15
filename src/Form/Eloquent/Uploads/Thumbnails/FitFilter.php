<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Intervention\Image\Image;

/**
 * @deprecated Use FitModifier instead
 */
class FitFilter extends ThumbnailFilter
{
    public function applyFilter(Image $image)
    {
        return (new FitModifier($this->params))
            ->apply($image);
    }

    public function resized()
    {
        return true;
    }
}
