<?php

namespace Code16\Sharp\Tests\Unit\Components;

use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Tests\Unit\Components\stubs\Media;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\Blade;


class ContentComponentTest extends SharpTestCase
{
    use InteractsWithViews;
    
    protected function setUp(): void
    {
        parent::setUp();
        view()->addNamespace('stub', __DIR__ . '/stubs');
    }
    
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
        
        $mediaComponent = view()->shared('media');
        
        $this->assertEquals($mediaComponent->attributes->getAttributes(), [
            'path' => 'storage/path.png',
            'width' => 500,
            'height' => 500,
        ]);
        
        $view->assertDontSee('<body');
        $view->assertSeeInOrder([
            '<div class="markdown">',
            '<p>Text</p>',
            '<div class="sharp-media"></div>',
            '</div>'
        ], false);
    }
}
