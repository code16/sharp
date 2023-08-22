<?php

namespace Code16\Sharp\View\Components;


use Illuminate\View\Component;

class Vite extends Component
{
    public function __construct(?string $hotFile = null)
    {
        \Illuminate\Support\Facades\Vite::useHotFile($hotFile ?? public_path('vendor/sharp/hot'));
    }

    public function render(): string
    {
        return '{{ $slot }}{{ $append() }}';
    }

    public function append(): void
    {
        \Illuminate\Support\Facades\Vite::useHotFile(public_path('hot')); // reset to default hot file location
    }
}
