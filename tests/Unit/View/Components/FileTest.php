<?php

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Illuminate\Foundation\Testing\Concerns\InteractsWithViews;
use Illuminate\Http\UploadedFile;
use Illuminate\View\ComponentAttributeBag;

uses(InteractsWithViews::class);

beforeEach(function () {
    Storage::fake('local');
});

uses(InteractsWithViews::class);

it('renders <x-sharp-file>', function () {
    $filePath = UploadedFile::fake()->create('doc.pdf')->storeAs('data', 'doc.pdf', 'local');

    $this->blade(
        sprintf('<x-sharp-file file="%s" :attributes="$attributes" />', e(json_encode([
            'name' => 'doc.pdf',
            'path' => $filePath,
            'disk' => 'local',
        ]))),
        [
            'attributes' => new ComponentAttributeBag([
                'class' => 'my-file',
            ]),
        ]
    )
        ->assertSee('my-file')
        ->assertSee('doc.pdf');
});

it('renders <x-sharp-file> legend', function () {
    $this->blade(
        sprintf('<x-sharp-file file="%s" legend="Legendary" />', e(json_encode([
            'name' => 'doc.pdf',
            'path' => UploadedFile::fake()->create('doc.pdf')->storeAs('data', 'doc.pdf', 'local'),
            'disk' => 'local',
        ]))),
    )
        ->assertSee('Legendary');
});

it('provides fileModel', function () {
    $filePath = UploadedFile::fake()->create('doc.pdf')->storeAs('data', 'doc.pdf', 'local');

    $component = $this->component(\Code16\Sharp\View\Components\File::class, [
        'file' => e(json_encode([
            'name' => 'doc.pdf',
            'path' => $filePath,
            'disk' => 'local',
        ])),
    ]);

    expect($component->fileModel)->toBeInstanceOf(SharpUploadModel::class)
        ->and($component->fileModel->file_name)->toBe($filePath)
        ->and($component->fileModel->disk)->toBe('local');
});
