<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Intervention\Image\Filters\FilterInterface;

abstract class ThumbnailFilter implements FilterInterface
{
    /**
     * @var array
     */
    protected $params;

    public function __construct(array $params)
    {
        $this->params = $params;
    }

    public function resized()
    {
        return false;
    }
}
