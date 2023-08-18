<?php

namespace Code16\Sharp\View\Components\Alert;

use Illuminate\View\Component;
use Illuminate\View\View;

class AssetsOutdated extends Component
{
    public function __construct()
    {
    }

    public function isAssetsOutdated(): bool
    {
        $distManifest = file_get_contents(__DIR__.'/../../../../resources/assets/dist/manifest.json');
        $publicManifest = file_get_contents(public_path('vendor/sharp/manifest.json'));

        return trim($distManifest ?: '') !== trim($publicManifest ?: '');
    }

    public function render(): View
    {
        return view('sharp::components.alert.assets-outdated', [
            'self' => $this,
        ]);
    }
}
