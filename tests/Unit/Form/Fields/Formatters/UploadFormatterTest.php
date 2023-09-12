<?php

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
});

it('allows to format value to front', function () {
    $formatter = new UploadFormatter;

    $field = SharpFormUploadField::make('upload');
    $this->assertEquals(
        [
            'name' => 'test.png',
            'path' => 'files/test.png',
            'disk' => 'local',
            'thumbnail' => 'path/of/thumbnail.png',
            'size' => 2000,
        ],
        $formatter
            ->toFront($field, [
                'name' => 'test.png',
                'path' => 'files/test.png',
                'disk' => 'local',
                'thumbnail' => 'path/of/thumbnail.png',
                'size' => 2000,
            ]),
    );
});

it('ignores_not_existing_file_from_front', function () {
    $formatter = new UploadFormatter;
    $field = SharpFormUploadField::make('upload');

    $this->assertEquals(
        [],
        $formatter->fromFront(
            $field,
            'attribute',
            [
                'name' => 'test.png',
            ]),
    );
});

it('stores_newly_uploaded_file_from_front', function () {
    $uploadedFile = UploadedFile::fake()
        ->image('image.jpg');

    $uploadedFile->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $field = SharpFormUploadField::make('upload')
        ->setStorageBasePath('data')
        ->setStorageDisk('local');

    $this->assertEquals(
        [
            'file_name' => 'data/image.jpg',
            'size' => $uploadedFile->getSize(),
            'mime_type' => 'image/jpeg',
            'disk' => 'local',
            'filters' => null,
        ],
        (new UploadFormatter)
            ->fromFront(
                $field,
                'attribute',
                [
                    'name' => '/image.jpg',
                    'uploaded' => true,
                ],
            ),
    );

    Storage::disk('local')->assertExists('data/image.jpg');
});

it('delays_execution_if_the_storage_path_contains_instance_id_in_a_store_case', function () {
    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $field = SharpFormUploadField::make('upload')
        ->setStorageDisk('local')
        ->setStorageBasePath('data/Test/{id}');

    $this->expectException(SharpFormFieldFormattingMustBeDelayedException::class);

    (new UploadFormatter)->fromFront(
        $field,
        'attribute',
        [
            'name' => '/image.jpg',
            'uploaded' => true,
        ],
    );
});

it('replaces the id placeholder if the storage path contains instance id in an update case', function () {
    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $field = SharpFormUploadField::make('upload')
        ->setStorageDisk('local')
        ->setStorageBasePath('data/Test/{id}');

    expect(
        (new UploadFormatter)
            ->setInstanceId(50)
            ->fromFront($field, 'attr', ['name' => 'image.jpg', 'uploaded' => true])
    )->toHaveKey('file_name', 'data/Test/50/image.jpg');

    Storage::disk('local')->assertExists('data/Test/50/image.jpg');
});

it('optimizes uploaded images if configured', function () {
    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    \Mockery::mock('alias:\Spatie\ImageOptimizer\OptimizerChainFactory')
        ->shouldReceive('create')
        ->once()
        ->andReturn(new class
        {
            public function optimize()
            {
                return true;
            }
        }, );

    $field = SharpFormUploadField::make('upload')
        ->shouldOptimizeImage()
        ->setStorageDisk('local');

    (new UploadFormatter)->fromFront($field, 'attribute', [
        'name' => 'image.jpg',
        'uploaded' => true,
    ]);
});

it('transforms the newly uploaded file if isTransformOriginal is configured', function () {
    $uploadedFile = UploadedFile::fake()->image('image.jpg', 600, 600);
    $uploadedFile->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $field = SharpFormUploadField::make('upload')
        ->setStorageDisk('local')
        ->setTransformable(true, false)
        ->setStorageBasePath('data/Test');

    $result = (new UploadFormatter)
        ->fromFront(
            $field,
            'attribute',
            [
                'name' => '/image.jpg',
                'uploaded' => true,
                'transformed' => true,
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
        );

    $this->assertEmpty($result['filters']);
    $this->assertNotEquals($uploadedFile->getSize(), $result['size']);
    Storage::disk('local')->assertExists('data/Test/image.jpg');
});

it('transforms an existing file if isTransformOriginal is configured', function () {
    $existingFile = UploadedFile::fake()->image('image.jpg', 600, 600);
    $existingFile->storeAs('/data/Test', 'image.jpg', ['disk' => 'local']);
    $originalSize = $existingFile->getSize();

    $field = SharpFormUploadField::make('upload')
        ->setStorageDisk('local')
        ->setTransformable(true, false)
        ->setStorageBasePath('data/Test');

    $this->assertEquals(
        [
            'filters' => [],
            'file_name' => 'data/Test/image-1.jpg',
            'disk' => 'local',
            'size' => 6467,
        ],
        (new UploadFormatter)
            ->fromFront(
                $field,
                'attribute',
                [
                    'name' => 'image.jpg',
                    'path' => 'data/Test/image.jpg',
                    'disk' => 'local',
                    'uploaded' => false,
                    'transformed' => true,
                    'size' => $originalSize,
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
            ),
    );

    $this->assertNotEquals(
        $originalSize,
        Storage::disk('local')->size('data/Test/image-1.jpg')
    );

    $this->assertFalse(Storage::disk('local')->exists('data/Test/image.jpg'));
});

it('allows to use a closure as storageBasePath', function () {
    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $path = '/some/path';

    $field = SharpFormUploadField::make('upload')
        ->setStorageBasePath(function () use (&$path) {
            return $path;
        })
        ->setCropRatio('16:9')
        ->setStorageDisk('local');

    $path = '/some/updated/path';

    expect(
        (new UploadFormatter)
            ->fromFront($field, 'attr', ['name' => '/image.jpg', 'uploaded' => true])
    )->toHaveKey('file_name', '/some/updated/path/image.jpg');
});

it('returns full object after no change was made if configured', function () {
    $field = SharpFormUploadField::make('upload')
        ->setStorageDisk('local')
        ->setTransformable()
        ->setStorageBasePath('data/Test');

    $value = [
        'name' => 'data/Test/image.jpg',
        'uploaded' => false,
        'transformed' => false,
    ];

    $this->assertEquals(
        $value,
        (new UploadFormatter)
            ->setAlwaysReturnFullObject()
            ->fromFront(
                $field,
                'attribute',
                $value,
            ),
    );
});

it('returns full object after only transformations if configured', function () {
    $field = SharpFormUploadField::make('upload')
        ->setStorageDisk('local')
        ->setTransformable()
        ->setStorageBasePath('data/Test');

    $value = [
        'name' => 'data/Test/image.jpg',
        'uploaded' => false,
        'transformed' => true,
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
    ];

    $this->assertEquals(
        $value,
        (new UploadFormatter)
            ->setAlwaysReturnFullObject()
            ->fromFront($field, 'attr', $value),
    );
});