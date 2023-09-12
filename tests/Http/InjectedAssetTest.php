<?php

it('allows to define assets to render in views', function () {
    $this->app['config']->set(
        'sharp.extensions.assets.head',
        ['/path/to/asset.css'],
    );

    $this->get(route('code16.sharp.login'))
        ->assertSee('<link rel="stylesheet" href="/path/to/asset.css">', false);
});