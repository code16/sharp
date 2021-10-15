<?php

namespace Code16\Sharp\View\Components;

use Code16\Sharp\View\Components\Content\Utils\ComponentAttributeBagCollection;
use Illuminate\Foundation\Application;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\Factory;
use JetBrains\PhpStorm\NoReturn;

class Content extends Component
{
    public ComponentAttributeBagCollection $contentComponentAttributes;
    
    public function __construct(
        public ?int $imageThumbnailWidth = null,
        public ?int $imageThumbnailHeight = null,
    ) {
        $this->contentComponentAttributes = new ComponentAttributeBagCollection();
        $this->contentComponentAttributes->put('sharp-image', [
            'thumbnail-width' => $this->imageThumbnailWidth,
            'thumbnail-height' => $this->imageThumbnailHeight,
        ]);
    }
    
    public function bind()
    {
        app()->singleton(static::class, function ($app) {
            return $this;
        });
    }
    
    public function unbind()
    {
        app()->offsetUnset(static::class);
    }

    public function render(): string
    {
        $this->bind();
        
        return '<x-sharp::content.render-content :content="$slot" />@php($unbind())';
    }
}
