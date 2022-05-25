<?php

namespace Code16\Sharp\Tests\Unit\Components;

use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\Components\stubs\Image;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\Blade;

class ContentComponentTest extends SharpTestCase
{
    use InteractsWithViews;

    /** @test */
    public function we_can_render_content()
    {
        Blade::component(Image::class, 'sharp-image');

        $view = $this->blade(<<<'blade'
                <x-sharp-content :image-thumbnail-width="500">
                    <x-sharp-content::attributes
                        component="sharp-image"
                        :thumbnail-height="500"
                    />
                    {!! $content !!}
                </x-sharp-content>
            blade, [
            'content' => <<<'blade'
                    <p>Text</p>
                    <x-sharp-image path="storage/path.png"></x-sharp-image>
                blade,
        ]);

        /**
         * @see Image
         */
        $imageComponent = view()->shared('sharp-image');

        $this->assertEquals(
            [
                'path' => 'storage/path.png',
                'thumbnail-width' => 500,
                'thumbnail-height' => 500,
            ],
            $imageComponent->attributes->getAttributes(),
        );

        $view->assertDontSee('<body');

        $view->assertSeeInOrder(
            [
                '<p>Text</p>',
                '<img class="sharp-image">',
            ],
            false,
        );
    }
}
