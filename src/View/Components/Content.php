<?php

namespace Code16\Sharp\View\Components;

use Illuminate\Foundation\Application;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\Factory;
use JetBrains\PhpStorm\NoReturn;

class Content extends Component
{
    
    public function __construct(
        public ?int $imageWidth = null,
        public ?int $imageHeight = null,
    ) {
        app()->singleton(static::class, function ($app) {
            return $this;
        });
    }
    
    public function rendered()
    {
       app()->offsetUnset(static::class);
    }
    
    #[NoReturn]
    public function addAttributes(string $component, ComponentAttributeBag $attributes)
    {
    
    }

    public function render(): string
    {
        return '<x-sharp::content.render-content :content="$slot" />@php($rendered())';
    }
}
