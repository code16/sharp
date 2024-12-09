<?php

namespace Code16\Sharp\View\Components;

use Illuminate\View\Component;

class ViteWrapper extends Component
{
    public function __construct(
        public ?string $hotFile = null
    ) {
        \Illuminate\Support\Facades\Vite::useHotFile($hotFile ?? base_path('vendor/code16/sharp/dist/hot'));
    }

    public function render(): callable
    {
        return function () {
            \Illuminate\Support\Facades\Vite::useHotFile(public_path('hot')); // reset to default hot file location

            return '{{ $slot }}';
        };
    }
}
