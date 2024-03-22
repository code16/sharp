<?php

use Code16\Sharp\Tests\Fixtures\Entities\PersonEntity;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('s3');
    Storage::fake('public');

    config()->set(
        'sharp.entities.person',
        PersonEntity::class,
    );

    login();
});

it('allows to get files info', function () {
    $this->withoutExceptionHandling();

    $file1 = UploadedFile::fake()->create('test.doc', Str::random());
    $file1->storeAs('/files/docs', 'test.doc', ['disk' => 'local']);

    $file2 = UploadedFile::fake()->create('test.pdf', Str::random(32));
    $file2->storeAs('/files/pdfs', 'test.pdf', ['disk' => 's3']);

    $this
        ->postJson(
            route('code16.sharp.api.files.show', [
                'entityKey' => 'person',
                'instanceId' => 1,
            ]),
            [
                'files' => [
                    [
                        'path' => '/files/docs/test.doc',
                        'disk' => 'local',
                    ], [
                        'path' => '/files/pdfs/test.pdf',
                        'disk' => 's3',
                    ],
                ],
                'thumbnail_width' => 400,
                'thumbnail_height' => 400,
            ],
        )
        ->assertOk()
        ->assertJson([
            'files' => [
                [
                    'name' => 'test.doc',
                    'path' => '/files/docs/test.doc',
                    'disk' => 'local',
                    'size' => 16,
                ], [
                    'name' => 'test.pdf',
                    'path' => '/files/pdfs/test.pdf',
                    'disk' => 's3',
                    'size' => 32,
                ],
            ],
        ]);
});

it('returns thumbnails if file is an image', function () {
    $this->withoutExceptionHandling();

    UploadedFile::fake()->create('test.doc', Str::random())
        ->storeAs('/files/docs', 'test.doc', ['disk' => 'local']);

    $file2 = createImage('s3');

    $this
        ->postJson(
            route('code16.sharp.api.files.show', [
                'entityKey' => 'person',
                'instanceId' => 1,
            ]),
            [
                'files' => [
                    [
                        'path' => '/files/docs/test.doc',
                        'disk' => 'local',
                    ], [
                        'path' => $file2,
                        'disk' => 's3',
                    ],
                ],
                'thumbnail_width' => 400,
                'thumbnail_height' => 400,
            ],
        )
        ->assertOk()
        ->assertJson([
            'files' => [
                [
                    'name' => 'test.doc',
                    'path' => '/files/docs/test.doc',
                    'disk' => 'local',
                    'size' => 16,
                ],
                [
                    'name' => basename($file2),
                    'path' => $file2,
                    'disk' => 's3',
                    'thumbnail' => sprintf(
                        '/storage/thumbnails/data/400-400_q-90/%s?%s',
                        basename($file2),
                        Storage::disk('public')
                            ->lastModified('/thumbnails/data/400-400_q-90/'.basename($file2))
                    ),
                    'size' => 1148,
                ],
            ],
        ]);
});

it('sends missing files with "not_found" attribute', function () {
    $this->withoutExceptionHandling();

    $file1 = UploadedFile::fake()->create('test.doc', Str::random());
    $file1->storeAs('/files/docs', 'test.doc', ['disk' => 'local']);

    $file2 = UploadedFile::fake()->create('test.pdf', Str::random(32));
    $file2->storeAs('/files/pdfs', 'test.pdf', ['disk' => 's3']);

    $this
        ->postJson(
            route('code16.sharp.api.files.show', [
                'entityKey' => 'person',
                'instanceId' => 1,
            ]),
            [
                'files' => [
                    [
                        'path' => '/files/docs/test.doc',
                        'disk' => 'local',
                    ], [
                        'path' => '/files/missing.jpg',
                        'disk' => 's3',
                    ], [
                        'path' => '/files/pdfs/test.pdf',
                        'disk' => 's3',
                    ], [
                        'path' => '/files/missing2.txt',
                        'disk' => 'local',
                    ],
                ],
                'thumbnail_width' => 400,
                'thumbnail_height' => 400,
            ],
        )
        ->assertOk()
        ->assertJson([
            'files' => [
                [
                    'name' => 'test.doc',
                    'path' => '/files/docs/test.doc',
                    'disk' => 'local',
                    'size' => 16,
                ], [
                    'path' => '/files/missing.jpg',
                    'disk' => 's3',
                    'not_found' => true,
                ], [
                    'name' => 'test.pdf',
                    'path' => '/files/pdfs/test.pdf',
                    'disk' => 's3',
                    'size' => 32,
                ], [
                    'path' => '/files/missing2.txt',
                    'disk' => 'local',
                    'not_found' => true,
                ],
            ],
        ]);
});
