<?php

namespace Code16\Sharp\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Layout extends Component
{
    public function render(): View
    {
        return view('sharp::layouts.app');
    }
}
