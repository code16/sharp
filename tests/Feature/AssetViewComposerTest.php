<?php

namespace Code16\Sharp\Tests\Feature;

use Code16\Sharp\Tests\Feature\Api\BaseApiTest;

class AssetViewComposerTest extends BaseApiTest
{
    /** @test */
    public function we_can_define_assets_to_render_in_views()
    {
        $this->withoutExceptionHandling();
        $this->buildTheWorld();

        $this->app['config']->set(
            'sharp.extensions.assets.head',
            ['/path/to/asset.css']
        );

        $this->get(route('code16.sharp.login'))
            ->assertViewHas('injectedAssets')
            ->assertSee('<link rel="stylesheet" href="/path/to/asset.css">');
    }
}