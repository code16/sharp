<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Http\Testing\FileFactory;
use Illuminate\Support\Facades\File;

class UploadFormatterTest extends SharpTestCase
{

    protected function setUp(): void
    {
        parent::setUp();

        config(['sharp.uploads.tmp_dir' => 'tmp']);

        config(['filesystems.disks.local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ]]);

        File::deleteDirectory(storage_path("app/tmp"));
        File::deleteDirectory(storage_path("app/data"));
    }

    /** @test */
    function we_can_format_value_to_front()
    {
        $formatter = new UploadFormatter;

        $field = SharpFormUploadField::make("upload");
        $this->assertEquals(["name" => "test.png"], $formatter->toFront($field, ["name" => "test.png"]));
    }

    /** @test */
    function we_ignore_existing_file_from_front()
    {
        $formatter = new UploadFormatter;
        $field = SharpFormUploadField::make("upload");
        $attribute = "attribute";

        $this->assertEquals([], $formatter->fromFront(
            $field, $attribute, ["name" => "test.png"])
        );
    }

    /** @test */
    function we_store_newly_uploaded_file_from_front()
    {
        $formatter = new UploadFormatter;
        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test");
        $attribute = "attribute";

        $file = $this->uploadedFile();

        $this->assertEquals([
            "file_name" => "data/Test/{$file[0]}",
            "size" => $file[1],
            "mime_type" => "image/png",
            "disk" => "local",
            "transformed" => false
        ], $formatter->fromFront(
            $field, $attribute, ["name" => $file[0], "uploaded" => true])
        );

        $this->assertFileExists(storage_path("app/data/Test/$file[0]"));
    }

    /** @test */
    function we_delay_execution_if_the_storage_path_contains_instance_id_in_a_store_case()
    {
        $formatter = new UploadFormatter;
        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test/{id}");

        $file = $this->uploadedFile();

        $this->expectException(SharpFormFieldFormattingMustBeDelayedException::class);
        $formatter->fromFront(
            $field, "attribute", ["name" => $file[0], "uploaded" => true]
        );
    }

    /** @test */
    function if_the_storage_path_contains_instance_id_in_an_update_case_we_replace_the_id_placeholder()
    {
        $formatter = new UploadFormatter;
        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test/{id}");

        $file = $this->uploadedFile();

        $this->assertArrayContainsSubset([
            "file_name" => "data/Test/50/{$file[0]}"
        ], $formatter->setInstanceId(50)->fromFront(
            $field, "attribute", ["name" => $file[0], "uploaded" => true]
        ));
    }

    /** @test */
    function we_handle_crop_transformation_on_upload_from_front()
    {
        $file = $this->uploadedFile();
        $formatter = new UploadFormatter;
        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test")
            ->setCropRatio("16:9");
        $attribute = "attribute";

        $this->assertArrayContainsSubset([
            "file_name" => "data/Test/{$file[0]}",
            "transformed" => true

        ], $formatter->fromFront(
            $field, $attribute, [
                "name" => $file[0], "cropData" => [
                    "height" => .8, "width" => .6, "x" => 0, "y" => .1, "rotate" => 0
                ], "uploaded" => true
            ]
        ));
    }

    /** @test */
    function we_handle_crop_transformation_on_a_previously_upload_from_front()
    {
        $file = (new FileFactory)->image("image.png", 600, 600);
        $filePath = $file->store("data/Test");

        $formatter = new UploadFormatter;
        $field = SharpFormUploadField::make("upload")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test");

        $this->assertArrayContainsSubset([
            "transformed" => true

        ], $formatter->fromFront(
            $field, "attribute", [
                "name" => $filePath, "cropData" => [
                    "height" => .8, "width" => .6, "x" => 0, "y" => .1, "rotate" => 0
                ], "uploaded" => false
            ]
        ));
    }

    /**
     * @return array
     */
    private function uploadedFile()
    {
        $file = (new FileFactory)->image("image.png", 600, 600);

        return [
            basename($file->store("tmp")),
            $file->getSize()
        ];
    }
}