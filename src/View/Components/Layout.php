<?php

namespace Code16\Sharp\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Vite;
use Illuminate\View\Component;

class Layout extends Component
{
    public function render(): View
    {
        if (Vite::hotFile() === public_path('/hot')) {
            Vite::useHotFile(public_path('vendor/sharp/hot')); // allow running "npm run dev" for symlinked assets
        }

        return view('sharp::layouts.app');
    }
}
