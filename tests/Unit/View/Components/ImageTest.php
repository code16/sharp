<?php

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\FitModifier;
use Code16\Sharp\Utils\Thumbnail;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\ComponentAttributeBag;

uses(InteractsWithViews::class);

beforeEach(function () {
    Storage::fake('local');
});

it('renders <x-sharp-image>', function () {
    $model = SharpUploadModel::make([
        'disk' => 'local',
        'file_name' => createImage(),
    ]);

    $modifier = new FitModifier(400, 400);

    $this
        ->blade(
            sprintf('<x-sharp-image file="%s" legend="Legendary" :attributes="$attributes" />', e(json_encode([
                'name' => 'test.png',
                'path' => $model->file_name,
                'disk' => 'local',
            ]))),
            [
                'attributes' => new ComponentAttributeBag([
                    'thumbnailWidth' => 400,
                    'thumbnailHeight' => 400,
                    'filters' => [$modifier],
                    'alt' => 'Image',
                ]),
            ]
        )
        ->assertSee(
            sprintf('<img src="%s" alt="Image">', Thumbnail::for($model)->addModifier($modifier)->setAppendTimestamp()->make(400, 400)),
            false
        )
        ->assertSee('Legendary');
});
