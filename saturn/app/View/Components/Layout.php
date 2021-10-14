<?php

namespace App\View\Components;

use Illuminate\View\Component;

class Layout extends Component
{
    public function __construct()
    {
    }

    public function render()
    {
        return view('layouts.app');
    }
}
