<?php

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\Uploads\Transformers\Fakes\FakePicturable;
use Code16\Sharp\Tests\Unit\Form\Eloquent\Utils\TestWithSharpUploadModel;
use Code16\Sharp\Tests\Unit\SharpEloquentBaseTest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

beforeEach(function() {
    config()->set('sharp.uploads.thumbnails_disk', 'public');
    config()->set('sharp.uploads.thumbnails_dir', 'thumbnails');

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
            'thumbnail' => $upload->thumbnail(200, 200),
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
                ],
                'id' => $upload1->id,
            ], [
                'file' => [
                    'name' => basename($upload2->file_name),
                    'path' => $upload2->file_name,
                    'disk' => 'local',
                    'size' => $upload2->size,
                    'thumbnail' => $upload2->thumbnail(200, 200),
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

    $this->assertEquals(
        [
            [
                'file' => [
                    'name' => basename($upload1->file_name),
                    'path' => $upload1->file_name,
                    'disk' => 'local',
                    'size' => $upload1->size,
                    'thumbnail' => $upload1->thumbnail(200, 200),
                    'filters' => $filters,
                ],
                'id' => $upload1->id,
            ], [
                'file' => [
                    'name' => basename($upload2->file_name),
                    'path' => $upload2->file_name,
                    'disk' => 'local',
                    'size' => $upload2->size,
                    'thumbnail' => $upload2->thumbnail(200, 200),
                ],
                'id' => $upload2->id,
            ],
        ],
        $transformer->apply('', $picturable, 'pictures'),
    );
});

it('allows to fake an sharpUpload and transform a single upload', function () {
    $file = createImage();

    $uploadData = [
        'file_name' => $file,
        'size' => 120,
        'disk' => 'local',
        'filters' => [],
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
            'filters' => [],
        ],
        $transformer->apply($uploadData, null, 'picture'),
    );
});

