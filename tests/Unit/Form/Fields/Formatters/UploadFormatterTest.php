<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class UploadFormatterTest extends SharpTestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        Storage::fake("local");
    }

    /** @test */
    function we_can_format_value_to_front()
    {
        $formatter = new UploadFormatter;

        $field = SharpFormUploadField::make("upload");
        $this->assertEquals([
            "name" => "test.png"
        ], $formatter->toFront($field, [
            "name" => "test.png"
        ]));
    }

    /** @test */
    function we_ignore_not_existing_file_from_front()
    {
        $formatter = new UploadFormatter;
        $field = SharpFormUploadField::make("upload");

        $this->assertEquals([], $formatter->fromFront(
            $field, "attribute", [
                "name" => "test.png"
            ])
        );
    }

    /** @test */
    function we_store_newly_uploaded_file_from_front()
    {
        $uploadedFile = UploadedFile::fake()
            ->image("image.jpg");
        
        $uploadedFile->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local");
        
        $this->assertEquals(
            [
                "file_name" => "data/image.jpg",
                "size" => $uploadedFile->getSize(),
                "mime_type" => 'image/jpeg',
                "disk" => "local",
                "transformed" => false
            ], 
            (new UploadFormatter)->fromFront(
                $field, 
                "attribute", 
                [
                    "name" => "/image.jpg",
                    "uploaded" => true
                ]
            )
        );

        Storage::disk('local')->assertExists("data/image.jpg");
    }

    /** @test */
    function we_delay_execution_if_the_storage_path_contains_instance_id_in_a_store_case()
    {
        UploadedFile::fake()
            ->image("image.jpg")
            ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test/{id}");

        $this->expectException(SharpFormFieldFormattingMustBeDelayedException::class);

        (new UploadFormatter)->fromFront(
            $field, 
            "attribute", 
            [
                "name" => "/image.jpg",
                "uploaded" => true
            ]
        );
    }

    /** @test */
    function if_the_storage_path_contains_instance_id_in_an_update_case_we_replace_the_id_placeholder()
    {
        UploadedFile::fake()
            ->image("image.jpg")
            ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test/{id}");

        $this->assertArrayContainsSubset(
            ["file_name" => "data/Test/50/image.jpg"], 
            (new UploadFormatter)->setInstanceId(50)->fromFront(
                $field, 
                "attribute", 
                [
                    "name" => "image.jpg",
                    "uploaded" => true
                ]
            )
        );

        Storage::disk('local')->assertExists("data/Test/50/image.jpg");
    }


    /** @test */
    function we_handle_crop_transformation_on_upload_from_front()
    {
        UploadedFile::fake()
            ->image("image.jpg")
            ->storeAs('/test', 'image.jpg', ['disk' => 'local']);

        $field = SharpFormUploadField::make("upload")
            ->setStorageBasePath("/test")
            ->setCropRatio("16:9")
            ->setStorageDisk("local");

        $this->assertArrayContainsSubset(
            ["transformed" => true],
            (new UploadFormatter)->fromFront(
                $field, 
                "attribute", 
                [
                    "name" => "/test/image.jpg",
                    "cropData" => [
                        "height" => .8, "width" => .6, "x" => 0, "y" => .1, "rotate" => 1
                    ],
                    "uploaded" => false
                ]
            )
        );
    }

    /** @test */
    function we_handle_crop_transformation_on_a_previously_upload_from_front()
    {
        UploadedFile::fake()
            ->image("image.jpg", 600, 600)
            ->storeAs('/data/test', 'image.jpg', ['disk' => 'local']);

        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local")
            ->setCropRatio("16:9")
            ->setStorageBasePath("data/test");

        $this->assertArrayContainsSubset(
            ["transformed" => true], 
            (new UploadFormatter)->fromFront(
                $field, 
                "attribute", [
                    "name" => "/data/test/image.jpg",
                    "cropData" => [
                        "height" => .8, "width" => .6, "x" => 0, "y" => .1, "rotate" => 1
                    ],
                    "uploaded" => false
                ]
            )
        );
    }

    /** @test */
    public function we_optimize_uploaded_images_if_configured()
    {
        UploadedFile::fake()
            ->image("image.jpg")
            ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

        \Mockery::mock('alias:\Spatie\ImageOptimizer\OptimizerChainFactory')
            ->shouldReceive('create')
            ->once()
            ->andReturn(new class {
                public function optimize()
                {
                    return true;
                }
            });
        
        $field = SharpFormUploadField::make("upload")
            ->shouldOptimizeImage()
            ->setStorageDisk("local");

        (new UploadFormatter)->fromFront($field, "attribute", [
            "name" => "image.jpg",
            "uploaded" => true
        ]);
    }
}
