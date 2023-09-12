<?php

use Code16\Sharp\Tests\Unit\View\Components\Fakes\Image;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\Blade;

uses(InteractsWithViews::class);

it('renders content', function () {
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
});
