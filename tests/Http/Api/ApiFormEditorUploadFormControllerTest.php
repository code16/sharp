<?php

use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config()->set('sharp.uploads.tmp_dir', 'tmp');
    Storage::fake('local');
    $this->withoutExceptionHandling();
});

it('can post a file and legend', function () {
    UploadedFile::fake()
        ->image('image.jpg', 600, 600)
        ->storeAs('tmp', 'image.jpg', ['disk' => 'local']);

    $editor = SharpFormEditorField::make('upload')
        ->allowUploads(
            SharpFormEditorUpload::make()
                ->setStorageDisk('local')
                ->setStorageBasePath('data/Posts/{id}')
                ->setImageOptimize()
                ->setImageTransformable(true, false)
                ->setHasLegend()
        );

    $this
        ->postJson(route('code16.sharp.api.form.editor.upload.form.update'), [
            'data' => [
                'file' => [
                    'name' => 'image.jpg',
                    'uploaded' => true,
                ],
                'legend' => 'Awesome image',
            ],
            'fields' => $editor->toArray()['uploads']['fields'],
        ])
        ->assertOk()
        ->assertExactJson([
            'file' => [
                'name' => 'image.jpg',
                'path' => 'data/Posts/'.UploadFormatter::ID_PLACEHOLDER.'/image.jpg',
                'disk' => 'local',
                'thumbnail' => null,
                'size' => 6467,
                'filters' => null,
                'id' => null,
                'uploaded' => true,
            ],
            'legend' => 'Awesome image',
        ]);
});
