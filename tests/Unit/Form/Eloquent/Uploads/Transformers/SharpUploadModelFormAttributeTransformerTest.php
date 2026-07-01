<?php

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Tests\Unit\Form\Eloquent\Uploads\Transformers\Fakes\FakePicturable;
use Code16\Sharp\Tests\Unit\Utils\FakesBreadcrumb;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

uses(FakesBreadcrumb::class);

beforeEach(function () {
    //    sharp()->config()->configureUploadsThumbnailCreation(
    //        thumbnailsDisk: 'public',
    //        thumbnailsDir: 'thumbnails',
    //    );

    Storage::fake('local');
    Storage::fake('public');
});

it('transforms a single upload', function () {
    $this->freezeTime(function (Carbon $time) {
        $upload = new SharpUploadModel([
            'file_name' => createImage(),
            'size' => 120,
            'mime_type' => 'image/png',
            'disk' => 'local',
        ]);
        $picturable = new FakePicturable(['id' => 1]);
        $picturable->setRelation('picture', $upload);

        $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1');

        $transformer = new SharpUploadModelFormAttributeTransformer();

        expect($transformer->apply('', $picturable, 'picture'))
            ->toEqual([
                'id' => $upload->id,
                'name' => basename($upload->file_name),
                'path' => $upload->file_name,
                'disk' => 'local',
                'size' => $upload->size,
                'thumbnail' => $upload->thumbnail(200, 200),
                'playable_preview_url' => null,
                'mime_type' => 'image/png',
                'download_url' => URL::temporarySignedRoute(
                    'code16.sharp.download.show',
                    $time->copy()->addMinutes(config('session.lifetime')),
                    [
                        'entityKey' => 'person',
                        'instanceId' => '1',
                        'disk' => 'local',
                        'path' => $upload->file_name,
                    ]
                ),
            ]);
    });
});

it('transforms a single upload with transformations', function () {
    $this->freezeTime(function (Carbon $time) {
        $upload = new SharpUploadModel([
            'file_name' => createImage(),
            'size' => 120,
            'mime_type' => 'image/png',
            'disk' => 'local',
            'filters' => [
                'crop' => [
                    'height' => .5,
                    'width' => .75,
                    'x' => .3,
                    'y' => .34,
                ],
                'rotate' => [
                    'angle' => 45,
                ],
            ],
        ]);
        $picturable = new FakePicturable(['id' => 1]);
        $picturable->setRelation('picture', $upload);

        $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1');

        $transformer = new SharpUploadModelFormAttributeTransformer();

        expect($transformer->apply('', $picturable, 'picture'))
            ->toEqual([
                'id' => $upload->id,
                'name' => basename($upload->file_name),
                'path' => $upload->file_name,
                'disk' => 'local',
                'size' => $upload->size,
                'mime_type' => 'image/png',
                'thumbnail' => $upload->thumbnail(200, 200),
                'playable_preview_url' => null,
                'filters' => [
                    'crop' => [
                        'height' => .5,
                        'width' => .75,
                        'x' => .3,
                        'y' => .34,
                    ],
                    'rotate' => [
                        'angle' => 45,
                    ],
                ],
                'download_url' => URL::temporarySignedRoute(
                    'code16.sharp.download.show',
                    $time->copy()->addMinutes(config('session.lifetime')),
                    [
                        'entityKey' => 'person',
                        'instanceId' => '1',
                        'disk' => 'local',
                        'path' => $upload->file_name,
                    ]
                ),
            ]);
    });
});

it('transforms a list of upload', function () {
    $this->freezeTime(function (Carbon $time) {
        $upload1 = new SharpUploadModel([
            'file_name' => createImage(),
            'size' => 120,
            'mime_type' => 'image/png',
            'disk' => 'local',
        ]);
        $upload2 = new SharpUploadModel([
            'file_name' => createImage(),
            'size' => 120,
            'mime_type' => 'image/png',
            'disk' => 'local',
        ]);
        $picturable = new FakePicturable(['id' => 1]);
        $picturable->setRelation('pictures', collect([$upload1, $upload2]));

        $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1');

        $transformer = new SharpUploadModelFormAttributeTransformer();

        expect($transformer->apply('', $picturable, 'pictures'))->toEqual([
            [
                'file' => [
                    'name' => basename($upload1->file_name),
                    'path' => $upload1->file_name,
                    'disk' => 'local',
                    'size' => $upload1->size,
                    'thumbnail' => $upload1->thumbnail(200, 200),
                    'playable_preview_url' => null,
                    'mime_type' => 'image/png',
                    'download_url' => URL::temporarySignedRoute(
                        'code16.sharp.download.show',
                        $time->copy()->addMinutes(config('session.lifetime')),
                        [
                            'entityKey' => 'person',
                            'instanceId' => '1',
                            'disk' => 'local',
                            'path' => $upload1->file_name,
                        ]
                    ),
                ],
                'id' => null,
            ],
            [
                'file' => [
                    'name' => basename($upload2->file_name),
                    'path' => $upload2->file_name,
                    'disk' => 'local',
                    'size' => $upload2->size,
                    'thumbnail' => $upload2->thumbnail(200, 200),
                    'playable_preview_url' => null,
                    'mime_type' => 'image/png',
                    'download_url' => URL::temporarySignedRoute(
                        'code16.sharp.download.show',
                        $time->copy()->addMinutes(config('session.lifetime')),
                        [
                            'entityKey' => 'person',
                            'instanceId' => '1',
                            'disk' => 'local',
                            'path' => $upload2->file_name,
                        ]
                    ),
                ],
                'id' => null,
            ],
        ]);
    });
});

it('transforms a list of upload with transformations', function () {
    $this->freezeTime(function (Carbon $time) {
        $filters = [
            'crop' => [
                'height' => .5,
                'width' => .75,
                'x' => .3,
                'y' => .34,
            ],
            'rotate' => [
                'angle' => 45,
            ],
        ];

        $upload1 = new SharpUploadModel([
            'file_name' => createImage(),
            'size' => 120,
            'mime_type' => 'image/png',
            'disk' => 'local',
            'filters' => $filters,
        ]);
        $upload2 = new SharpUploadModel([
            'file_name' => createImage(),
            'size' => 120,
            'mime_type' => 'image/png',
            'disk' => 'local',
        ]);
        $picturable = new FakePicturable(['id' => 1]);
        $picturable->setRelation('pictures', collect([$upload1, $upload2]));

        $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1');

        $transformer = new SharpUploadModelFormAttributeTransformer();

        expect($transformer->apply('', $picturable, 'pictures'))->toEqual([
            [
                'file' => [
                    'name' => basename($upload1->file_name),
                    'path' => $upload1->file_name,
                    'disk' => 'local',
                    'size' => $upload1->size,
                    'thumbnail' => $upload1->thumbnail(200, 200),
                    'playable_preview_url' => null,
                    'filters' => $filters,
                    'mime_type' => 'image/png',
                    'download_url' => URL::temporarySignedRoute(
                        'code16.sharp.download.show',
                        $time->copy()->addMinutes(config('session.lifetime')),
                        [
                            'entityKey' => 'person',
                            'instanceId' => '1',
                            'disk' => 'local',
                            'path' => $upload1->file_name,
                        ]
                    ),
                ],
                'id' => null,
            ],
            [
                'file' => [
                    'name' => basename($upload2->file_name),
                    'path' => $upload2->file_name,
                    'disk' => 'local',
                    'size' => $upload2->size,
                    'thumbnail' => $upload2->thumbnail(200, 200),
                    'playable_preview_url' => null,
                    'mime_type' => 'image/png',
                    'download_url' => URL::temporarySignedRoute(
                        'code16.sharp.download.show',
                        $time->copy()->addMinutes(config('session.lifetime')),
                        [
                            'entityKey' => 'person',
                            'instanceId' => '1',
                            'disk' => 'local',
                            'path' => $upload2->file_name,
                        ]
                    ),
                ],
                'id' => null,
            ],
        ]);
    });
});

it('transforms an upload with playable preview', function () {
    $this->freezeTime(function (Carbon $time) {
        $file = UploadedFile::fake()->create('video.mp4', 120, 'video/mp4');
        $path = $file->storeAs('data', 'video.mp4', ['disk' => 'local']);
        $upload = new SharpUploadModel([
            'file_name' => $path,
            'size' => 120,
            'mime_type' => 'video/mp4',
            'disk' => 'local',
        ]);
        $picturable = new FakePicturable(['id' => 1]);
        $picturable->setRelation('video', $upload);

        $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1');

        $transformer = new SharpUploadModelFormAttributeTransformer(withPlayablePreview: true);

        expect($transformer->apply('', $picturable, 'video'))
            ->toEqual([
                'id' => $upload->id,
                'name' => basename($upload->file_name),
                'path' => $upload->file_name,
                'disk' => 'local',
                'size' => $upload->size,
                'thumbnail' => null,
                'playable_preview_url' => '/'.$upload->file_name.'?expiration='.$time->copy()->addMinutes(30)->timestamp,
                'mime_type' => 'video/mp4',
                'download_url' => URL::temporarySignedRoute(
                    'code16.sharp.download.show',
                    $time->copy()->addMinutes(config('session.lifetime')),
                    [
                        'entityKey' => 'person',
                        'instanceId' => '1',
                        'disk' => 'local',
                        'path' => $upload->file_name,
                    ]
                ),
            ]);
    });
});

it('transforms a list of upload with playable preview', function () {
    $this->freezeTime(function (Carbon $time) {
        $file = UploadedFile::fake()->create('audio.mp3', 120, 'audio/mp3');
        $path = $file->storeAs('data', 'audio.mp3', ['disk' => 'local']);
        $upload1 = new SharpUploadModel([
            'file_name' => $path,
            'size' => 120,
            'mime_type' => 'audio/mp3',
            'disk' => 'local',
        ]);
        $picturable = new FakePicturable(['id' => 1]);
        $picturable->setRelation('songs', collect([$upload1]));

        $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1');

        $transformer = new SharpUploadModelFormAttributeTransformer(withPlayablePreview: true);

        expect($transformer->apply('', $picturable, 'songs'))->toEqual([
            [
                'file' => [
                    'name' => basename($upload1->file_name),
                    'path' => $upload1->file_name,
                    'disk' => 'local',
                    'size' => $upload1->size,
                    'thumbnail' => null,
                    'playable_preview_url' => '/'.$upload1->file_name.'?expiration='.$time->copy()->addMinutes(30)->timestamp,
                    'mime_type' => 'audio/mp3',
                    'download_url' => URL::temporarySignedRoute(
                        'code16.sharp.download.show',
                        $time->copy()->addMinutes(config('session.lifetime')),
                        [
                            'entityKey' => 'person',
                            'instanceId' => '1',
                            'disk' => 'local',
                            'path' => $upload1->file_name,
                        ]
                    ),
                ],
                'id' => $upload1->id,
            ],
        ]);
    });
});

describe('dynamicInstance', function () {
    it('allows to fake a sharpUpload and transform a single upload', function () {
        $this->freezeTime(function (Carbon $time) {
            $file = createImage();

            $uploadData = [
                'file_name' => $file,
                'size' => 120,
                'disk' => 'local',
                'filters' => [],
                'mime_type' => 'image/png',
                'width' => 100,
                'height' => 100,
            ];

            $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1');

            $transformer = (new SharpUploadModelFormAttributeTransformer())->dynamicInstance();

            expect($transformer->apply($uploadData, null, 'picture'))->toEqual([
                'id' => null,
                'name' => basename($file),
                'path' => $file,
                'disk' => 'local',
                'size' => 120,
                'thumbnail' => (new SharpUploadModel($uploadData))->thumbnail(200, 200),
                'playable_preview_url' => null,
                'filters' => [],
                'mime_type' => 'image/png',
                'width' => 100,
                'height' => 100,
                'download_url' => URL::temporarySignedRoute(
                    'code16.sharp.download.show',
                    $time->copy()->addMinutes(config('session.lifetime')),
                    [
                        'entityKey' => 'person',
                        'instanceId' => '1',
                        'disk' => 'local',
                        'path' => $file,
                    ]
                ),
            ]);
        });
    });

    it('sends "uploaded" and "transformed" attributes if present', function () {
        $file = createImage();

        $uploadData = [
            'file_name' => $file,
            'size' => 120,
            'disk' => 'local',
            'filters' => [],
            'uploaded' => true,
            'transformed' => true,
        ];

        $transformer = (new SharpUploadModelFormAttributeTransformer())->dynamicInstance();

        expect($transformer->apply($uploadData, null, 'picture'))
            ->toMatchArray([
                'uploaded' => true,
                'transformed' => true,
            ]);
    });
});
