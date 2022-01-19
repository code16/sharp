<?php

namespace Code16\Sharp\View\Components\Content;

use Code16\Sharp\View\Components\Content;
use Illuminate\View\Component;

class Attributes extends Component
{
    public function __construct(
        public Content $content,
        public string $component,
    ) {
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
