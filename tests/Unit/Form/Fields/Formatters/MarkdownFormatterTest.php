<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\MarkdownFormatter;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormMarkdownField;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Http\Testing\FileFactory;
use Illuminate\Support\Facades\File;
use Mockery;

class MarkdownFormatterTest extends SharpTestCase
{

    protected function setUp()
    {
        parent::setUp();

        config(['filesystems.disks.local' => [
            'driver' => 'local',
            'root' => storage_path('app'),
        ]]);

        config(['filesystems.disks.sharp_uploads' => [
            'driver' => 'local',
            'root' => storage_path('app/tmp'),
        ]]);

        config(['sharp.uploads.thumbnails_dir' => 'thumbnails']);

        File::deleteDirectory(storage_path("app/data"));
        File::deleteDirectory(public_path("thumbnails"));
    }

    /** @test */
    function we_can_format_value_to_front()
    {
        $formatter = new MarkdownFormatter;
        $field = SharpFormMarkdownField::make("md");
        $value = str_random();

        $this->assertEquals(["text" => $value], $formatter->toFront($field, $value));
    }

    /** @test */
    function we_can_format_a_text_with_an_embedded_upload_to_front()
    {
        $formatter = new MarkdownFormatter;
        $field = SharpFormMarkdownField::make("md");
        $value = "![](test.png)";

        $this->assertEquals(
            "test.png",
            $formatter->toFront($field, $value)["files"][0]["name"]);
    }

    /** @test */
    function we_can_transform_a_text_with_many_embedded_upload()
    {
        $formatter = new MarkdownFormatter;
        $field = SharpFormMarkdownField::make("md");
        $value = "![](test.png)\n![](test2.png)\n![](test3.png)";

        $this->assertCount(3, $formatter->toFront($field, $value)["files"]);
    }

    /** @test */
    function we_send_the_file_size_and_thumbnail_to_front()
    {
        $file = (new FileFactory)->image("test.png", 600, 600);
        $image = $file->store("data");

        $formatter = new MarkdownFormatter;
        $field = SharpFormMarkdownField::make("md");
        $value = "![]($image)";

        $this->assertEquals([
            "name" => $image,
            "size" => 1127,
            "thumbnail" => url("thumbnails/data/-150/" . basename($image))
        ], $formatter->toFront($field, $value)["files"][0]);
    }

    /** @test */
    function we_can_format_value_from_front()
    {
        $formatter = new MarkdownFormatter;
        $field = SharpFormMarkdownField::make("md");
        $value = str_random();
        $attribute = "attribute";

        $this->assertEquals($value, $formatter->fromFront($field, $attribute, ["text"=>$value]));
    }

    /** @test */
    function we_store_newly_uploaded_files_from_front()
    {
        $formatter = new MarkdownFormatter;
        $field = SharpFormMarkdownField::make("md");
        $value = "![](new_test.png)";
        $attribute = "attribute";

        $service = Mockery::mock();
        $service->shouldReceive('fromFront')
            ->andReturn([
                "file_name" => "uploaded_test.png",
            ]);

        app()->bind(UploadFormatter::class, function() use($service) {
            return $service;
        });


        $this->assertEquals(
            "![](uploaded_test.png)",
            $formatter->fromFront($field, $attribute, [
                "text" => $value,
                "files" => [[
                    "name" => "new_test.png",
                    "uploaded" => true
                ]]
            ])
        );
    }

}