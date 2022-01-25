<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadFormatterTest extends SharpTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake('local');
    }

    /** @test */
    public function we_can_format_value_to_front()
    {
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
    }

    /** @test */
    public function we_ignore_not_existing_file_from_front()
    {
        $formatter = new UploadFormatter;
        $field = SharpFormUploadField::make('upload');

        $this->assertEquals(
            [],
            $formatter->fromFront(
                $field,
                'attribute',
                [
                    'name' => 'test.png',
                ], ),
            );
    }

    /** @test */
    public function we_store_newly_uploaded_file_from_front()
    {
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
    }

    /** @test */
    public function we_delay_execution_if_the_storage_path_contains_instance_id_in_a_store_case()
    {
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
    }

    /** @test */
    public function if_the_storage_path_contains_instance_id_in_an_update_case_we_replace_the_id_placeholder()
    {
        UploadedFile::fake()
            ->image('image.jpg')
            ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

        $field = SharpFormUploadField::make('upload')
            ->setStorageDisk('local')
            ->setStorageBasePath('data/Test/{id}');

        $this->assertArraySubset(
            ['file_name' => 'data/Test/50/image.jpg'],
            (new UploadFormatter)->setInstanceId(50)->fromFront(
                $field,
                'attribute',
                [
                    'name' => 'image.jpg',
                    'uploaded' => true,
                ],
            ),
        );

        Storage::disk('local')->assertExists('data/Test/50/image.jpg');
    }

    /** @test */
    public function we_optimize_uploaded_images_if_configured()
    {
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
    }

    /** @test */
    public function we_transform_the_newly_uploaded_file_if_isTransformOriginal_is_configured()
    {
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
    }

    /** @test */
    public function we_transform_an_existing_file_if_isTransformOriginal_is_configured()
    {
        $existingFile = UploadedFile::fake()->image('image.jpg', 600, 600);
        $existingFile->storeAs('/data/Test', 'image.jpg', ['disk' => 'local']);
        $originalSize = $existingFile->getSize();

        $field = SharpFormUploadField::make('upload')
            ->setStorageDisk('local')
            ->setTransformable(true, false)
            ->setStorageBasePath('data/Test');

        $this->assertEquals(
            ['filters' => []],
            (new UploadFormatter)
                ->fromFront(
                    $field,
                    'attribute',
                    [
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
                    ],
                ),
        );

        $this->assertNotEquals($originalSize, Storage::disk('local')->size('data/Test/image.jpg'));
    }

    /** @test */
    public function we_get_use_a_closure_as_storageBasePath()
    {
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

        $this->assertArraySubset(
            ['file_name' => '/some/updated/path/image.jpg'],
            (new UploadFormatter)->fromFront(
                $field,
                'attribute',
                [
                    'name' => '/image.jpg',
                    'uploaded' => true,
                ],
            ),
        );
    }
}
