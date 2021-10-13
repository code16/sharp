<?php

namespace Code16\Sharp\Tests\Unit\Components\stubs;

use Code16\Sharp\View\Components\Content;
use Illuminate\View\Component;


class Media extends Component
{
    public function __construct()
    {
        view()->share('media', $this);
    }
    
    public function render()
    {
        return view('stub::media');
    }
}
