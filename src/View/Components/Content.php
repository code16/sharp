<?php

namespace Code16\Sharp\View\Components;

use Code16\LaravelContentRenderer\View\Components\Content as ContentComponent;

class Content extends ContentComponent
{
    public function __construct(
        public ?int $imageThumbnailWidth = null,
        public ?int $imageThumbnailHeight = null,
    ) {
        parent::__construct();
        $this->contentComponentAttributes->put('sharp-image', [
            'thumbnail-width' => $this->imageThumbnailWidth,
            'thumbnail-height' => $this->imageThumbnailHeight,
        ]);
    }
}
