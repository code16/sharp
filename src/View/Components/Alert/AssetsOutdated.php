<?php

namespace Code16\Sharp\View\Components\Alert;

use Code16\Sharp\View\Components\Vite as SharpViteComponent;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AssetsOutdated extends Component
{
    public function isAssetsOutdated(): bool
    {
        if (file_exists(app(SharpViteComponent::class)->hotFile)) {
            return false;
        }

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
