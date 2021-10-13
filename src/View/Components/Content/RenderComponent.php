<?php

namespace Code16\Sharp\View\Components\Content;


use Code16\Sharp\View\Components\Content;
use Code16\Sharp\View\Components\Content\Utils\ComponentFragment;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\View;
use JetBrains\PhpStorm\Pure;

class RenderComponent extends Component
{

    public function __construct(
        public ComponentFragment $fragment,
        public Content $content,
    ) {
    }
    
    public function resolveComponentName(): string
    {
        return $this->fragment->getComponentName();
    }
    
    public function resolveAttributes(): ComponentAttributeBag
    {
        $componentName = $this->fragment->getComponentName();
        $attributes = new ComponentAttributeBag($this->fragment->getComponentAttributes());
        
        if($contentAttributes = $this->content->contentComponentAttributes->get($componentName)) {
            $attributes = $attributes->merge($contentAttributes->getAttributes());
        }
        
        return $attributes;
    }
    
    public function render(): View
    {
        return view('sharp::components.content.render-component');
    }
}
