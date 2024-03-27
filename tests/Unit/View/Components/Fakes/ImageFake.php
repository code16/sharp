<?php

namespace Code16\Sharp\Tests\Unit\View\Components\Fakes;

use Illuminate\View\Component;

class ImageFake extends Component
{
    public static self $lastRendered;

    public function __construct()
    {
        view()->share('sharp-image', $this);
    }

    public function render()
    {
        return '<img class="sharp-image">';
    }
}
