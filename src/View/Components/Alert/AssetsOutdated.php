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
        $distPath = __DIR__ . '/../../../../dist';
        $distManifest = file_get_contents("$distPath/manifest.json");
        $publicManifest = file_get_contents(public_path('vendor/sharp/manifest.json'));

        if(file_exists("$distPath/hot")) {
            return false;
        }

        return trim($distManifest ?: '') !== trim($publicManifest ?: '');
    }

    public function render(): View
    {
        return view('sharp::components.alert.assets-outdated', [
            'self' => $this,
        ]);
    }
}
