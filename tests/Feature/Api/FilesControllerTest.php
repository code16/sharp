<?php

namespace Code16\Sharp\Tests\Feature\Api;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class FilesControllerTest extends BaseApiTest
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
        Storage::fake('s3');
        Storage::fake('public');
        $this->login();
        $this->disableSharpAuthorizationChecks();
    }

    /** @test */
    public function we_can_get_files_info()
    {
        $this->withoutExceptionHandling();

        $file1 = UploadedFile::fake()->create('test.doc', Str::random(16));
        $file1->storeAs('/files/docs', 'test.doc', ['disk' => 'local']);

        $file2 = UploadedFile::fake()->create('test.pdf', Str::random(32));
        $file2->storeAs('/files/pdfs', 'test.pdf', ['disk' => 's3']);

        $this
            ->withHeader(
                'referer',
                url('/sharp/s-list/person/s-form/download/1'),
            )
            ->postJson(
                route('code16.sharp.api.files.show', [
                    'entityKey' => 'download',
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
    }

    /** @test */
    public function we_can_get_thumbnails_if_file_is_image()
    {
        $this->withoutExceptionHandling();

        $file1 = UploadedFile::fake()->create('test.doc', Str::random(16));
        $file1->storeAs('/files/docs', 'test.doc', ['disk' => 'local']);

        $file2 = UploadedFile::fake()->image('test.jpg', 600, 600);
        $file2->storeAs('/files/images', 'test.jpg', ['disk' => 's3']);

        Carbon::setTestNow(now()->startOfSecond());

        $this
            ->withHeader(
                'referer',
                url('/sharp/s-list/person/s-form/download/1'),
            )
            ->postJson(
                route('code16.sharp.api.files.show', [
                    'entityKey' => 'download',
                    'instanceId' => 1,
                ]),
                [
                    'files' => [
                        [
                            'path' => '/files/docs/test.doc',
                            'disk' => 'local',
                        ], [
                            'path' => '/files/images/test.jpg',
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
                        'name' => 'test.jpg',
                        'path' => '/files/images/test.jpg',
                        'disk' => 's3',
                        'thumbnail' => sprintf(
                            '/storage/thumbnails/files/images/400-400/test.jpg?%s',
                            now()->getTimestamp()
                        ),
                        'size' => 6467,
                    ],
                ],
            ]);
    }

    /** @test */
    public function missing_files_are_stripped_out()
    {
        $this->withoutExceptionHandling();

        $file1 = UploadedFile::fake()->create('test.doc', Str::random(16));
        $file1->storeAs('/files/docs', 'test.doc', ['disk' => 'local']);

        $file2 = UploadedFile::fake()->create('test.pdf', Str::random(32));
        $file2->storeAs('/files/pdfs', 'test.pdf', ['disk' => 's3']);

        $this
            ->withHeader(
                'referer',
                url('/sharp/s-list/person/s-form/download/1'),
            )
            ->postJson(
                route('code16.sharp.api.files.show', [
                    'entityKey' => 'download',
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
                        'name' => 'test.pdf',
                        'path' => '/files/pdfs/test.pdf',
                        'disk' => 's3',
                        'size' => 32,
                    ],
                ],
            ]);
    }
}
