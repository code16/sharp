<?php

namespace Code16\Sharp\View\Components\Content;


use Code16\Sharp\View\Components\Content\Utils\ComponentFragment;
use Illuminate\View\Component;

class RenderComponent extends Component
{
    public ComponentFragment $fragment;

    public function __construct(ComponentFragment $fragment)
    {
        $this->fragment = $fragment;
    }

    public function render(): callable
    {
        $this->withName($this->fragment->getComponentName());
        
        return function ($data) {
            $this->withAttributes($this->fragment->getComponentAttributes());
            return 'sharp::components.content.render-component';
        };
    }
}
