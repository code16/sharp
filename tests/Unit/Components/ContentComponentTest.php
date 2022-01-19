<?php

namespace Code16\Sharp\Tests\Unit\Components;

use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\Components\stubs\Image;
use Code16\Sharp\View\Components\Content;
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
                    <div class="markdown">
                        <p>Text</p>
                        <x-sharp-image path="storage/path.png"></x-sharp-image>
                    </div>
                blade,
        ]);

        // Retrieve component @see to Image::__construct()
        [$imageComponent] = view()->shared('sharp-image');

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
                '<div class="markdown">',
                '<p>Text</p>',
                '<img class="sharp-image">',
                '</div>',
            ],
            false,
        );

        $this->assertFalse(app()->has(Content::class));
    }
}
