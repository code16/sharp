<?php

namespace Code16\Sharp\View\Components\Content;

use Code16\Sharp\View\Components\Content;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\View;

class Attributes extends Component
{
    public function __construct(
        public string $component,
    ) {
    }
    
    public function addAttributes(Content $content, ComponentAttributeBag $attributes)
    {
        $content->contentComponentAttributes->put(
            $this->component,
            $attributes,
        );
    }
    
    public function render(): View
    {
        return view('sharp::components.content.attributes');
    }
}
