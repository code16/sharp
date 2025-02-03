<?php

use Code16\Sharp\Config\SharpConfigBuilder;
use Illuminate\Foundation\Vite;
use Illuminate\Support\HtmlString;

it('allows to define assets to render in views', function () {
    $this->swap(Vite::class, new class() extends Vite
    {
        public function __invoke($entrypoints, $buildDirectory = null)
        {
            return new HtmlString(collect($entrypoints)->implode("\n"));
        }
    });

    app(SharpConfigBuilder::class)->loadViteAssets([
        'path/to/asset.css',
        'path/to/asset2.css',
    ]);
    app(SharpConfigBuilder::class)->loadStaticCss('static.css');

    $this->withoutExceptionHandling();

    $this->get(route('code16.sharp.login'))
        ->assertSeeInOrder(['path/to/asset.css', 'path/to/asset2.css', 'static.css']);
});
