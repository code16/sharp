<?php

namespace Code16\Sharp\View\Components;

use Illuminate\View\Component;

class Vite extends Component
{
    public function __construct(
        public ?string $hotFile = null
    ) {
        \Illuminate\Support\Facades\Vite::useHotFile($hotFile ?? public_path('vendor/sharp/hot'));
    }

    public function render(): callable
    {
        return function () {
            \Illuminate\Support\Facades\Vite::useHotFile(public_path('hot')); // reset to default hot file location

            return '{{ $slot }}';
        };
    }
}
