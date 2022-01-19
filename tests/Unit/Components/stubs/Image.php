<?php

namespace Code16\Sharp\Tests\Unit\Components\stubs;

use Illuminate\View\Component;

class Image extends Component
{
    public function __construct()
    {
        $components = view()->shared('sharp-image');
        $components[] = $this;
        view()->share('sharp-image', $components);
    }

    public function render()
    {
        return '<img class="sharp-image">';
    }
}
