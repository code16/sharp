<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

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
        Storage::disk('local')->put('tmp/toto', 'toto');

        $formatter = new UploadFormatter;
        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local");

        $this->assertEquals([
            "file_name" => "data/toto",
            "size" => 4,
            "mime_type" => 'text/plain',
            "disk" => "local",
            "transformed" => false
        ], $formatter->fromFront(
            $field, "attribute", [
                "name" => "toto",
                "uploaded" => true
            ])
        );

        Storage::disk('local')->assertExists("data/toto");
    }

    /** @test */
    function we_delay_execution_if_the_storage_path_contains_instance_id_in_a_store_case()
    {
        Storage::disk('local')->put('tmp/toto', 'toto');

        $formatter = new UploadFormatter;
        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test/{id}");

        $this->expectException(SharpFormFieldFormattingMustBeDelayedException::class);

        $formatter->fromFront(
            $field, "attribute", [
                "name" => "toto",
                "uploaded" => true
            ]
        );
    }

    /** @test */
    function if_the_storage_path_contains_instance_id_in_an_update_case_we_replace_the_id_placeholder()
    {
        Storage::disk('local')->put('tmp/toto', 'toto');

        $formatter = new UploadFormatter;
        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test/{id}");

        $this->assertArrayContainsSubset([
            "file_name" => "data/Test/50/toto"
        ], $formatter->setInstanceId(50)->fromFront(
            $field, "attribute", [
                "name" => "toto",
                "uploaded" => true
            ]
        ));

        Storage::disk('local')->assertExists("data/Test/50/toto");
    }


    /** @test */
    function we_handle_crop_transformation_on_upload_from_front()
    {
        Storage::disk('local')->put('toto', 'toto');

        $formatter = new UploadFormatter;

        $mock = \Mockery::mock(ImageManager::class);
        $mock
            ->shouldReceive('make')
            ->once()
            ->andReturn(new Class{
                public function crop(){
                    return true;
                }
                public function encode(){
                    return true;
                }
                public function rotate(){
                    return true;
                }
                public function width() {
                        return true;
                }
                public function height() {
                        return true;
                }
            });

        $formatter->setImageManager($mock);

        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local");

        $this->assertArrayContainsSubset([
            "transformed" => true
        ], $formatter->fromFront(
            $field, "attribute", [
                "name" => "toto",
                "cropData" => [
                    "height" => .8, "width" => .6, "x" => 0, "y" => .1, "rotate" => 1
                ],
                "uploaded" => false
            ]
        ));
    }

    /**
     * @test
     */
    function we_handle_crop_transformation_on_a_previously_upload_from_front()
    {
        Storage::disk('local')->put('tmp/toto', 'toto');

        $formatter = new UploadFormatter;

        $mock = \Mockery::mock(ImageManager::class);
        $mock
            ->shouldReceive('make')
            ->once()
            ->andReturn(new Class{
                public function crop(){
                    return true;
                }
                public function encode(){
                    return true;
                }
                public function rotate(){
                    return true;
                }
                public function width() {
                    return true;
                }
                public function height() {
                    return true;
                }
            });

        $formatter->setImageManager($mock);

        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local");

        $this->assertArrayContainsSubset([
            "transformed" => true
        ], $formatter->fromFront(
            $field, "attribute", [
                "name" => "toto",
                "cropData" => [
                    "height" => .8, "width" => .6, "x" => 0, "y" => .1, "rotate" => 1
                ],
                "uploaded" => true
            ]
        ));
    }

    /**
     * @test
     */
    public function we_should_optimize()
    {
        Storage::disk('local')->put('tmp/toto', 'toto');

        \Mockery::mock('alias:\Spatie\ImageOptimizer\OptimizerChainFactory')
            ->shouldReceive('create')
            ->once()
            ->andReturn(new class {
                public function optimize()
                {
                    return true;
                }
            });

        $formatter = new UploadFormatter;
        $field = SharpFormUploadField::make("upload")
            ->shouldOptimizeImage()
            ->setStorageDisk("local");

        $formatter->fromFront($field, "attribute", [
            "name" => "toto",
            "uploaded" => true
        ]);
    }
}
