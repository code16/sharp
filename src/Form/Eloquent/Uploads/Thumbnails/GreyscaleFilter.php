<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Intervention\Image\Image;

class GreyscaleFilter extends ThumbnailFilter
{

    /**
     * Applies filter effects to given image
     *
     * @param  Image $image
     * @return Image
     */
    public function applyFilter(Image $image)
    {
        $image->greyscale();

        return $image;
    }
}
