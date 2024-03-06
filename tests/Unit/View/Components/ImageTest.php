<?php

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Illuminate\View\ComponentAttributeBag;

beforeEach(function () {
    Storage::fake('local');
});

it('renders <x-sharp-image>', function () {
    $model = SharpUploadModel::make([
        'disk' => 'local',
        'file_name' => createImage(),
    ]);

    $filters = ['fit' => ['w' => 400, 'h' => 400]];

    $this->blade(
        sprintf('<x-sharp-image file="%s" legend="Legendary" :attributes="$attributes" />', e(json_encode([
            'name' => 'test.png',
            'path' => $model->file_name,
            'disk' => 'local',
        ]))),
        [
            'attributes' => new ComponentAttributeBag([
                'thumbnailWidth' => 400,
                'thumbnailHeight' => 400,
                'filters' => $filters,
                'alt' => 'Image',
            ]),
        ]
    )
        ->assertSee(
            sprintf('<img src="%s" alt="Image">', $model->thumbnail(400, 400, $filters)),
            false
        )->assertSee('Legendary');
});
