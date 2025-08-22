<?php

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Tests\Unit\Form\Eloquent\Uploads\Transformers\Fakes\FakePicturable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    //    sharp()->config()->configureUploadsThumbnailCreation(
    //        thumbnailsDisk: 'public',
    //        thumbnailsDir: 'thumbnails',
    //    );

    Storage::fake('local');
    Storage::fake('public');
});

it('transforms a single upload', function () {
    $upload = new SharpUploadModel([
        'file_name' => createImage(),
        'size' => 120,
        'mime_type' => 'image/png',
        'disk' => 'local',
    ]);
    $picturable = new FakePicturable(['id' => 1]);
    $picturable->setRelation('picture', $upload);

    $transformer = new SharpUploadModelFormAttributeTransformer();

    $this->assertEquals(
        [
            'id' => $upload->id,
            'name' => basename($upload->file_name),
            'path' => $upload->file_name,
            'disk' => 'local',
            'size' => $upload->size,
            'thumbnail' => $upload->thumbnail(200, 200),
            'playable_preview_url' => null,
            'mime_type' => 'image/png',
        ],
        $transformer->apply('', $picturable, 'picture'),
    );
});

it('transforms a single upload with transformations', function () {
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

    $transformer = new SharpUploadModelFormAttributeTransformer();

    $this->assertEquals(
        [
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
        ],
        $transformer->apply('', $picturable, 'picture'),
    );
});

it('transforms a list of upload', function () {
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

    $transformer = new SharpUploadModelFormAttributeTransformer();

    $this->assertEquals(
        [
            [
                'file' => [
                    'name' => basename($upload1->file_name),
                    'path' => $upload1->file_name,
                    'disk' => 'local',
                    'size' => $upload1->size,
                    'thumbnail' => $upload1->thumbnail(200, 200),
                    'playable_preview_url' => null,
                    'mime_type' => 'image/png',
                ],
                'id' => $upload1->id,
            ], [
                'file' => [
                    'name' => basename($upload2->file_name),
                    'path' => $upload2->file_name,
                    'disk' => 'local',
                    'size' => $upload2->size,
                    'thumbnail' => $upload2->thumbnail(200, 200),
                    'playable_preview_url' => null,
                    'mime_type' => 'image/png',
                ],
                'id' => $upload2->id,
            ],
        ],
        $transformer->apply('', $picturable, 'pictures'),
    );
});

it('transforms a list of upload with transformations', function () {
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

    $transformer = new SharpUploadModelFormAttributeTransformer();

    expect($transformer->apply('', $picturable, 'pictures'))
        ->toEqual([
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
                ],
                'id' => $upload1->id,
            ], [
                'file' => [
                    'name' => basename($upload2->file_name),
                    'path' => $upload2->file_name,
                    'disk' => 'local',
                    'size' => $upload2->size,
                    'thumbnail' => $upload2->thumbnail(200, 200),
                    'playable_preview_url' => null,
                    'mime_type' => 'image/png',
                ],
                'id' => $upload2->id,
            ],
        ]);
});

it('transforms an upload with playable preview', function () {
    $this->freezeTime(function (Carbon $time) {
        $upload = new SharpUploadModel([
            'file_name' => UploadedFile::fake()->create('video.mp4', 120, 'video/mp4'),
            'size' => 120,
            'mime_type' => 'video/mp4',
            'disk' => 'local',
        ]);
        $picturable = new FakePicturable(['id' => 1]);
        $picturable->setRelation('video', $upload);

        $transformer = new SharpUploadModelFormAttributeTransformer(withPlayablePreview: true);

        expect($transformer->apply('', $picturable, 'video'))
            ->toEqual([
                'id' => $upload->id,
                'name' => basename($upload->file_name),
                'path' => $upload->file_name,
                'disk' => 'local',
                'size' => $upload->size,
                'thumbnail' => null,
                'playable_preview_url' => $upload->file_name.'?expiration='.$time->addMinutes(30)->timestamp,
                'mime_type' => 'video/mp4',
            ]);
    });
});

it('transforms a list of upload with playable preview', function () {
    $this->freezeTime(function (Carbon $time) {
        $upload1 = new SharpUploadModel([
            'file_name' => UploadedFile::fake()->create('audio.mp3', 120, 'audio/mp3'),
            'size' => 120,
            'mime_type' => 'audio/mp3',
            'disk' => 'local',
        ]);
        $picturable = new FakePicturable(['id' => 1]);
        $picturable->setRelation('songs', collect([$upload1]));

        $transformer = new SharpUploadModelFormAttributeTransformer(withPlayablePreview: true);

        expect($transformer->apply('', $picturable, 'songs'))->toEqual([
            [
                'file' => [
                    'name' => basename($upload1->file_name),
                    'path' => $upload1->file_name,
                    'disk' => 'local',
                    'size' => $upload1->size,
                    'thumbnail' => null,
                    'playable_preview_url' => $upload1->file_name.'?expiration='.$time->addMinutes(30)->timestamp,
                    'mime_type' => 'audio/mp3',
                ],
                'id' => $upload1->id,
            ],
        ]);
    });
});

describe('dynamicInstance', function () {
    it('allows to fake a sharpUpload and transform a single upload', function () {
        $file = createImage();

        $uploadData = [
            'file_name' => $file,
            'size' => 120,
            'disk' => 'local',
            'filters' => [],
            'mime_type' => 'image/png',
        ];

        $transformer = (new SharpUploadModelFormAttributeTransformer())->dynamicInstance();

        $this->assertEquals(
            [
                'id' => null,
                'name' => basename($file),
                'path' => $file,
                'disk' => 'local',
                'size' => 120,
                'thumbnail' => (new SharpUploadModel($uploadData))->thumbnail(200, 200),
                'playable_preview_url' => null,
                'filters' => [],
                'mime_type' => 'image/png',
            ],
            $transformer->apply($uploadData, null, 'picture'),
        );
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
