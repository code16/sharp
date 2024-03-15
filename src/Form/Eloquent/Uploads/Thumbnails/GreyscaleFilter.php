<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Intervention\Image\Image;

/**
 * @deprecated use GreyscaleModifier instead
 */
class GreyscaleFilter extends ThumbnailFilter
{
    public function applyFilter(Image $image)
    {
        return (new GreyscaleModifier($this->params))
             ->apply($image);
    }
}
