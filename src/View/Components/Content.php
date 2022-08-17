<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\View\Utils\Content\ComponentAttributeBagCollection;
use Illuminate\View\Component;

class Content extends Component
{
    public ComponentAttributeBagCollection $contentComponentAttributes;
    public self $contentComponent;

    public function __construct(
        public ?int $imageThumbnailWidth = null,
        public ?int $imageThumbnailHeight = null,
    ) {
        $this->contentComponentAttributes = new ComponentAttributeBagCollection();
        $this->contentComponentAttributes->put('sharp-image', [
            'thumbnail-width' => $this->imageThumbnailWidth,
            'thumbnail-height' => $this->imageThumbnailHeight,
        ]);
        $this->contentComponent = $this;
    }

    public function render(): string
    {
        return '<x-sharp::content.render-content :content="$slot" />';
    }
}
