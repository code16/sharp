<?php

namespace Code16\Sharp\View\Components\Content;

use Code16\Sharp\View\Components\Content;
use Illuminate\View\Component;
use Illuminate\View\ComponentAttributeBag;
use Illuminate\View\View;

class Attributes extends Component
{
    public Content $content;
    
    public function __construct(
        public string $component,
    ) {
        $this->content = view()->getConsumableComponentData('contentComponent');
    }
    
    public function render(): callable
    {
        return function () {
            $this->content->contentComponentAttributes->put(
                $this->component,
                $this->attributes,
            );
        };
    }
}
