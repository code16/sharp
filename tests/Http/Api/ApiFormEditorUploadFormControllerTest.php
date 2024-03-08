<?php

use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    config()->set('sharp.uploads.tmp_dir', 'tmp');
    Storage::fake('local');
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
                ->setHasLegend()
        );

    $this
        ->postJson(route('code16.sharp.api.form.editor.upload.form.update', ['person']), [
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
                'exists' => false,
            ],
            'legend' => 'Awesome image',
        ]);
});

it('can post a file with known instanceId', function () {
    UploadedFile::fake()
        ->image('image.jpg', 600, 600)
        ->storeAs('tmp', 'image.jpg', ['disk' => 'local']);

    $editor = SharpFormEditorField::make('upload')
        ->allowUploads(
            SharpFormEditorUpload::make()
                ->setStorageDisk('local')
                ->setStorageBasePath('data/Posts/{id}')
        );

    $this
        ->postJson(route('code16.sharp.api.form.editor.upload.form.update', ['person', 1]), [
            'data' => [
                'file' => [
                    'name' => 'image.jpg',
                    'uploaded' => true,
                ],
            ],
            'fields' => $editor->toArray()['uploads']['fields'],
        ])
        ->assertOk()
        ->assertJson([
            'file' => [
                'name' => 'image.jpg',
                'path' => 'data/Posts/1/image.jpg',
                'disk' => 'local',
            ],
        ]);
});

it('fail if no file provided', function () {
    $this->withExceptionHandling();
    $editor = SharpFormEditorField::make('upload')
        ->allowUploads(
            SharpFormEditorUpload::make()
                ->setStorageDisk('local')
                ->setStorageBasePath('data/Posts/{id}')
        );

    $this
        ->postJson(route('code16.sharp.api.form.editor.upload.form.update', ['person', 1]), [
            'data' => [
                'file' => null,
            ],
            'fields' => $editor->toArray()['uploads']['fields'],
        ])
        ->assertStatus(422)
        ->assertJsonValidationErrorFor('file');
});
