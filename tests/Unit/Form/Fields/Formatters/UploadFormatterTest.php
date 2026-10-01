<?php

use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

beforeEach(function () {
    Storage::fake('local');
});

it('allows to format value to front', function () {
    $formatter = app(UploadFormatter::class);

    $field = SharpFormUploadField::make('upload');
    $this->assertEquals(
        [
            'name' => 'test.png',
            'path' => 'files/test.png',
            'disk' => 'local',
            'path_signature' => uploadPathSignature('local', 'files/test.png'),
            'thumbnail' => 'path/of/thumbnail.png',
            'size' => 2000,
        ],
        $formatter
            ->toFront($field, [
                'name' => 'test.png',
                'path' => 'files/test.png',
                'disk' => 'local',
                'path_signature' => uploadPathSignature('local', 'files/test.png'),
                'thumbnail' => 'path/of/thumbnail.png',
                'size' => 2000,
            ]),
    );
});

it('format existing file from front', function () {
    $formatter = app(UploadFormatter::class);
    $field = SharpFormUploadField::make('upload');

    $this->assertEquals(
        [
            'file_name' => 'data/Post/1/test.png',
            'mime_type' => 'image/png',
            'size' => 1000,
            'disk' => 'local',
            'filters' => ['rotate' => ['angle' => 10]],
        ],
        $formatter->fromFront(
            $field,
            'attribute',
            [
                'path' => 'data/Post/1/test.png',
                'path_signature' => uploadPathSignature('local', 'data/Post/1/test.png'),
                'mime_type' => 'image/png',
                'size' => 1000,
                'disk' => 'local',
                'filters' => ['rotate' => ['angle' => 10]],
            ]),
    );
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
        ->setImageCropRatio('16:9')
        ->setStorageDisk('local');

    $path = '/some/updated/path';

    expect(
        app(UploadFormatter::class)
            ->fromFront($field, 'attr', ['name' => '/image.jpg', 'uploaded' => true])
    )->toHaveKey('file_name', '/some/updated/path/image.jpg');
});

it('returns full object after transformations', function () {
    $field = SharpFormUploadField::make('upload')
        ->setStorageDisk('local')
        ->setImageTransformable()
        ->setStorageBasePath('data/Test');

    $value = [
        'path' => 'data/Test/image.jpg',
        'disk' => 'local',
        'path_signature' => uploadPathSignature('local', 'data/Test/image.jpg'),
        'size' => 120,
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

    expect(app(UploadFormatter::class)->fromFront($field, 'attr', $value))
        ->toEqual([
            'file_name' => 'data/Test/image.jpg',
            'disk' => 'local',
            'size' => 120,
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
});

it('format temporary upload from front', function () {
    $formatter = app(UploadFormatter::class);

    UploadedFile::fake()
        ->image('image.jpg', width: 100, height: 100)
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $field = SharpFormUploadField::make('upload')->setStorageTemporary();

    expect(
        $formatter
            ->fromFront($field, 'attr', [
                'name' => 'image.jpg',
                'uploaded' => true,
            ])
    )
        ->toEqual([
            'file_name' => 'tmp/image.jpg',
            'disk' => 'local',
            'mime_type' => 'image/jpeg',
            'size' => 887,
            'width' => 100,
            'height' => 100,
        ]);
});

it('format image from front with width and height', function () {
    $formatter = app(UploadFormatter::class);

    UploadedFile::fake()
        ->image('image.jpg', width: 100, height: 100)
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    $field = SharpFormUploadField::make('upload');

    expect(
        $formatter
            ->fromFront($field, 'attr', [
                'name' => 'image.jpg',
                'uploaded' => true,
            ])
    )
        ->toEqual([
            'file_name' => 'data/image.jpg',
            'disk' => 'local',
            'mime_type' => 'image/jpeg',
            'size' => 887,
            'width' => 100,
            'height' => 100,
        ]);
});

describe('path signature', function () {
    it('adds a path signature when formatting an existing file to front', function () {
        $field = SharpFormUploadField::make('upload');

        expect(app(UploadFormatter::class)->toFront($field, ['path' => 'data/Post/1/test.png', 'disk' => 'local']))
            ->toHaveKey('path_signature', uploadPathSignature('local', 'data/Post/1/test.png'));
    });

    it('rejects an existing file without signature', function () {
        $field = SharpFormUploadField::make('upload')->setStorageDisk('local');

        expect(fn () => app(UploadFormatter::class)->fromFront($field, 'attr', [
            'path' => 'private/secret-credentials.svg',
            'disk' => 'local',
        ]))->toThrow(ValidationException::class, trans('sharp::errors.invalid_file_signature'));
    });

    it('rejects a transformed file without signature', function () {
        $field = SharpFormUploadField::make('upload')->setStorageDisk('local');

        app(UploadFormatter::class)->fromFront($field, 'attr', [
            'path' => 'private/secret-credentials.svg',
            'disk' => 'local',
            'transformed' => true,
            'filters' => ['rotate' => ['angle' => 90]],
        ]);
    })->throws(ValidationException::class);

    it('rejects an existing file with a signature of another path or disk', function (string $path, string $disk) {
        $field = SharpFormUploadField::make('upload')->setStorageDisk('local');

        app(UploadFormatter::class)->fromFront($field, 'attr', [
            'path' => $path,
            'disk' => $disk,
            'path_signature' => uploadPathSignature('local', 'data/Post/1/test.png'),
        ]);
    })
        ->with([
            'other path' => ['private/secret-credentials.svg', 'local'],
            'other disk' => ['data/Post/1/test.png', 'public'],
        ])
        ->throws(ValidationException::class);

    it('accepts a signature made with a previous app key', function () {
        $previousKey = str_repeat('a', 32);
        $signature = hash_hmac('sha256', "sharp-upload-path\0local\0data/Post/1/test.png", $previousKey);

        config(['app.previous_keys' => ['base64:'.base64_encode($previousKey)]]);
        app()->forgetInstance('encrypter');

        expect(
            app(UploadFormatter::class)->fromFront(
                SharpFormUploadField::make('upload')->setStorageDisk('local'),
                'attr',
                ['path' => 'data/Post/1/test.png', 'disk' => 'local', 'path_signature' => $signature],
            )
        )->toHaveKey('file_name', 'data/Post/1/test.png');
    });

    it('does not check signatures when disabled', function () {
        sharp()->config()->disableUploadPathSigning();

        $field = SharpFormUploadField::make('upload')->setStorageDisk('local');

        expect(
            app(UploadFormatter::class)->fromFront($field, 'attr', ['path' => 'data/Post/1/test.png', 'disk' => 'local'])
        )->toHaveKey('file_name', 'data/Post/1/test.png');

        expect(app(UploadFormatter::class)->toFront($field, ['path' => 'data/Post/1/test.png', 'disk' => 'local']))
            ->not->toHaveKey('path_signature');
    });

    it('rejects an uploaded file name containing a traversal', function () {
        app(UploadFormatter::class)->fromFront(
            SharpFormUploadField::make('upload')->setStorageTemporary(),
            'attr',
            ['name' => '../private/secret-credentials.svg', 'uploaded' => true],
        );
    })->throws(ValidationException::class);

    describe('temporary storage', function () {
        it('keeps a new temporary upload in the tmp directory without any signature', function () {
            UploadedFile::fake()
                ->image('image.jpg', width: 100, height: 100)
                ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

            expect(
                app(UploadFormatter::class)->fromFront(
                    SharpFormUploadField::make('upload')->setStorageTemporary(),
                    'attr',
                    ['name' => 'image.jpg', 'uploaded' => true],
                )
            )
                ->toHaveKey('file_name', 'tmp/image.jpg')
                ->toHaveKey('disk', 'local');
        });

        it('accepts an existing file wherever the application stored it, once signed', function () {
            $field = SharpFormUploadField::make('upload')->setStorageTemporary();
            $formatter = app(UploadFormatter::class);

            // The file was moved by the application: neither the disk nor the path are known by Sharp
            $front = $formatter->toFront($field, ['path' => 'final/dir/image.jpg', 'disk' => 'public', 'size' => 12]);

            expect($formatter->fromFront($field, 'attr', $front))
                ->toEqual(['file_name' => 'final/dir/image.jpg', 'disk' => 'public', 'size' => 12]);
        });

        it('accepts a transformation of an existing file, once signed', function () {
            $field = SharpFormUploadField::make('upload')->setStorageTemporary();

            expect(
                app(UploadFormatter::class)->fromFront($field, 'attr', signedUpload([
                    'path' => 'final/dir/image.jpg',
                    'disk' => 'public',
                    'transformed' => true,
                    'filters' => ['rotate' => ['angle' => 90]],
                ]))
            )->toEqual([
                'file_name' => 'final/dir/image.jpg',
                'disk' => 'public',
                'filters' => ['rotate' => ['angle' => 90]],
            ]);
        });

        it('rejects an existing file which is not signed', function () {
            app(UploadFormatter::class)->fromFront(
                SharpFormUploadField::make('upload')->setStorageTemporary(),
                'attr',
                ['path' => 'private/secret-credentials.svg', 'disk' => 'local'],
            );
        })->throws(ValidationException::class);
    });

    it('rejects an uploaded file name containing a backslash', function () {
        app(UploadFormatter::class)->fromFront(
            SharpFormUploadField::make('upload')->setStorageTemporary(),
            'attr',
            ['name' => 'tmp\\..\\private\\secret-credentials.svg', 'uploaded' => true],
        );
    })->throws(ValidationException::class);

    it('rejects an uploaded file whose destination is outside of the storage base path', function () {
        UploadedFile::fake()->image('image.jpg')->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

        app(UploadFormatter::class)->fromFront(
            SharpFormUploadField::make('upload')->setStorageDisk('local')->setStorageBasePath('data/../private'),
            'attr',
            ['name' => 'image.jpg', 'uploaded' => true],
        );
    })->throws(ValidationException::class);
});
