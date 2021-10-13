<?php

namespace Code16\Sharp\Tests\Unit\Components\stubs;

use Code16\Sharp\View\Components\Content;
use Illuminate\View\Component;


class Media extends Component
{
    public function __construct()
    {
        $components = view()->shared('sharp-media');
        $components[] = $this;
        view()->share('sharp-media', $components);
    }
    
    public function render()
    {
        return '<div class="sharp-media"></div>';
    }
}
