<?php

namespace Code16\Sharp\Tests\Unit\Components;

use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\Components\stubs\Media;
use Code16\Sharp\View\Components\Content;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\Blade;

class ContentComponentTest extends SharpTestCase
{
    use InteractsWithViews;
    
    /** @test */
    function we_can_render_content()
    {
        Blade::component(Media::class, 'sharp-media');
        
        $view = $this->blade(<<<blade
            <x-sharp-content :image-width="500">
                <x-sharp-content::attributes
                    component="sharp-media"
                    :height="500"
                />
                {!! \$content !!}
            </x-sharp-content>
        blade, [
            'content' => <<<blade
                <div class="markdown">
                    <p>Text</p>
                    <x-sharp-media path="storage/path.png"></x-sharp-media>
                </div>
            blade,
        ]);
        
        // Retrieve component @see to Media::__construct()
        [$mediaComponent] = view()->shared('sharp-media');
        
        $this->assertEquals(
            [
                'path' => 'storage/path.png',
                'width' => 500,
                'height' => 500,
            ],
            $mediaComponent->attributes->getAttributes(),
        );
        
        $view->assertDontSee('<body');
        
        $view->assertSeeInOrder(
            [
                '<div class="markdown">',
                '<p>Text</p>',
                '<div class="sharp-media"></div>',
                '</div>'
            ], 
            false
        );
    
        $this->assertFalse(app()->has(Content::class));
    }
}
