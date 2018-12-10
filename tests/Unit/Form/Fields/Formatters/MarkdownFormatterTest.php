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
    function we_can_format_a_text_value_to_front()
    {
        $formatter = new MarkdownFormatter;
        $field = SharpFormMarkdownField::make("md");
        $value = str_random();

        $this->assertEquals(["text" => $value], $formatter->toFront($field, $value));
    }

    /** @test */
    function when_text_has_an_embedded_upload_the_files_array_is_handled_to_front()
    {
        $image = (new FileFactory)->image("test.png")->store("data");

        $formatter = new MarkdownFormatter;
        $field = SharpFormMarkdownField::make("md");
        $value = "![](local:$image)";

        $this->assertEquals(
            "local:$image",
            $formatter->toFront($field, $value)["files"][0]["name"]);
    }

    /** @test */
    function when_text_has_multiple_embedded_uploads_the_files_array_is_handled_to_front()
    {
        $image1 = (new FileFactory)->image("test.png")->store("data");
        $image2 = (new FileFactory)->image("test2.png")->store("data");
        $image3 = (new FileFactory)->image("test3.png")->store("data");

        $formatter = new MarkdownFormatter;
        $field = SharpFormMarkdownField::make("md");
        $value = "![](local:$image1)\n![](local:$image2)\n![](local:$image3)";

        $this->assertCount(3, $formatter->toFront($field, $value)["files"]);
    }

    /** @test */
    function we_send_the_file_size_and_thumbnail_to_front()
    {
        $file = (new FileFactory)->image("test.png", 600, 600);
        $image = $file->store("data");

        $formatter = new MarkdownFormatter;
        $field = SharpFormMarkdownField::make("md");
        $value = "![](local:$image)";

        $toFrontArray = $formatter->toFront($field, $value)["files"][0];

        $this->assertEquals("local:$image", $toFrontArray["name"]);
        $this->assertTrue($toFrontArray["size"] > 0);
        $this->assertStringStartsWith(url("thumbnails/data/1000-400/" . basename($image)), $toFrontArray["thumbnail"]);
    }

    /** @test */
    function we_can_format_a_text_value_from_front()
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
        $value = "![](local:new_test.png)";
        $attribute = "attribute";

        $service = Mockery::mock();
        $service->shouldReceive('setInstanceId')
            ->andReturnSelf();
        $service->shouldReceive('fromFront')
            ->andReturn([
                "file_name" => "uploaded_test.png",
            ]);

        app()->bind(UploadFormatter::class, function() use($service) {
            return $service;
        });

        $this->assertEquals(
            "![](local:uploaded_test.png)",
            $formatter->fromFront($field, $attribute, [
                "text" => $value,
                "files" => [[
                    "name" => "local:new_test.png",
                    "uploaded" => true
                ]]
            ])
        );
    }

    /** @test */
    function files_are_handled_for_a_localized_markdown()
    {
        $formatter = new MarkdownFormatter;
        $field = SharpFormMarkdownField::make("md")->setLocalized();
        $value = [
            "fr" => "![](local:test_fr.png)\n![](local:test2_fr.png)",
            "en" => "![](local:test_en.png)",
        ];

        $this->assertCount(3, $formatter->toFront($field, $value)["files"]);
    }
}