<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Intervention\Image\Image;

class FitFilter extends ThumbnailFilter
{
    /**
     * Applies filter effects to given image.
     *
     * @param  Image  $image
     * @return Image
     */
    public function applyFilter(Image $image)
    {
        $image->fit($this->params['w'], $this->params['h']);

        return $image;
    }

    public function resized()
    {
        return true;
    }
}
