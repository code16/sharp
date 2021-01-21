<?php

namespace Code16\Sharp\Tests\Unit\Utils\Transformers;

use Code16\Sharp\Tests\SharpTestCase;
use Code16\Sharp\Utils\Transformers\Attributes\MarkdownAttributeTransformer;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class MarkdownAttributeTransformerTest extends SharpTestCase
{
    /** @test */
    function we_can_transform_a_text_to_markdown()
    {
        $object = (object)[
            "text" => "some basic *markdown* **test**"
        ];
        
        $this->assertEquals(
            "<p>some basic <em>markdown</em> <strong>test</strong></p>",
            (new MarkdownAttributeTransformer())->apply($object->text, $object, "text")
        );
    }

    /** @test */
    function we_can_generate_thumbnails_for_visuals()
    {
        Storage::fake("local");

        UploadedFile::fake()
            ->image("test.jpg", 100, 100)
            ->storeAs("markdown", "test.jpg", "local");

        $object = (object)[
            "text" => '<img src="local:markdown/test.jpg" alt="" />'
        ];

        $this->assertStringStartsWith(
            sprintf('<img src="/storage/%s/markdown/10-/test.jpg?', config("sharp.uploads.thumbnails_dir")),
            (new MarkdownAttributeTransformer())->handleImages(10)->apply($object->text, $object, "text")
        );
    }
}