<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Tests\Fixtures\Person;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Testing\FileFactory;
use Illuminate\Support\Facades\File;

class UploadFormatterTest extends SharpFormEloquentBaseTest
{

    protected function setUp()
    {
        parent::setUp();

        config(['filesystems.disks.sharp_uploads' => [
            'driver' => 'local',
            'root' => storage_path('app/tmp'),
        ]]);

        config(['filesystems.disks.local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ]]);

        File::deleteDirectory(storage_path("app/tmp"));
        File::deleteDirectory(storage_path("app/data"));
    }

    /** @test */
    function we_can_format_value()
    {
        $file = $this->uploadedFile();
        $formatter = app(UploadFormatter::class);

        $formField = SharpFormUploadField::make("file")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test");

        $this->assertEquals([
                "file_name" => "data/Test/{$file[0]}",
                "size" => $file[1],
                "mime_type" => "image/png",
                "disk" => "local",
                "transformed" => false
            ],
            $formatter->format([
                "name" => $file[0],
                "uploaded" => true
            ], $formField, new Person)
        );
    }

    /** @test */
    function newly_uploaded_file_is_moved_to_the_configured_disk_and_directory()
    {
        $fileName = $this->uploadedFile()[0];
        $formatter = app(UploadFormatter::class);

        $formField = SharpFormUploadField::make("file")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test");

        $formatter->format([
            "name" => $fileName,
            "uploaded" => true
        ], $formField, new Person);

        $this->assertTrue(file_exists(storage_path("app/data/Test/$fileName")));
    }

    /** @test */
    function we_throw_an_exception_if_a_newly_uploaded_file_needs_a_modelId_in_path_with_a_non_existing_model()
    {
        $fileName = $this->uploadedFile()[0];
        $formatter = app(UploadFormatter::class);

        $formField = SharpFormUploadField::make("file")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test/{id}");

        $this->expectException(ModelNotFoundException::class);

        $formatter->format([
            "name" => $fileName,
            "uploaded" => true
        ], $formField, new Person());
    }

    /** @test */
    function we_substitute_parameters_with_model_attributes_in_storage_base_path()
    {
        $fileName = $this->uploadedFile()[0];
        $formatter = app(UploadFormatter::class);

        $formField = SharpFormUploadField::make("file")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test/{id}/{name}");

        $person = Person::create([
            "name" => "john"
        ]);

        $this->assertArraySubset(
            ["file_name" => "data/Test/{$person->id}/{$person->name}/$fileName"],
            $formatter->format([
                "name" => $fileName,
                "uploaded" => true
            ], $formField, $person)
        );
    }

    /** @test */
    function we_handle_crop_transformation_on_upload()
    {
        $file = $this->uploadedFile();
        $formatter = app(UploadFormatter::class);

        $formField = SharpFormUploadField::make("file")
            ->setStorageDisk("local")
            ->setStorageBasePath("data/Test")
            ->setCropRatio("16:9");

        $this->assertArraySubset([
            "file_name" => "data/Test/{$file[0]}",
            "transformed" => true
        ],
            $formatter->format([
                "name" => $file[0],
                "uploaded" => true,
                "cropData" => [
                    "height" => .8, "width" => .6, "x" => 0, "y" => .1, "rotate" => 0
                ]
            ], $formField, new Person)
        );
    }

//    /** @test */
//    function upload_files_are_deleted_on_remove()
//    {
//        $fileName = $this->uploadedFile()[0];
//        $formatter = app(UploadFormatter::class);
//
//        $formField = SharpFormUploadField::make("file")
//            ->setStorageDisk("local")
//            ->setStorageBasePath("data/Test");
//
//        $formatter->format([
//            "name" => $fileName,
//            "uploaded" => true
//        ], $formField, new Person);
//
//        $formatter->format(null, $formField, new Person);
//
//        $this->assertFalse(file_exists(storage_path("app/data/Test/$fileName")));
//    }

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