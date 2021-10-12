<?php

namespace Code16\Sharp\View\Components;

use Illuminate\View\Component;

class Content extends Component
{
    public function __construct()
    {
    }

    public function render(): string
    {
        return '<x-sharp::content.render-content :content="$slot" />';
    }
}
