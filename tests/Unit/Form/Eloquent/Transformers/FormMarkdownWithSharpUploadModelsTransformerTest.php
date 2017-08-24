<?php

namespace Code16\Sharp\Tests\Unit\Form\Eloquent\Transformers;

use Code16\Sharp\Form\Eloquent\Transformers\FormMarkdownWithSharpUploadModelsTransformer;
use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Tests\Unit\Form\Eloquent\SharpFormEloquentBaseTest;
use Code16\Sharp\Tests\Unit\Form\Eloquent\Utils\TestWithSharpUploadModel;

class FormMarkdownWithSharpUploadModelsTransformerTest extends SharpFormEloquentBaseTest
{
    use TestWithSharpUploadModel;

    /** @test */
    function we_can_transform_a_markdown_field_with_an_embedded_upload()
    {
        $file = $this->createImage();

        $model = new SomeModelWithMarkdown(1, "abc\n![]({$file})");
        $upload = $this->createSharpUploadModel($file, $model, "markdown_text");

        $transformer = new FormMarkdownWithSharpUploadModelsTransformer(SharpUploadModel::class);

        $this->assertEquals([
            "text" => "abc\n![]($file)",
            "files" => [
                [
                    "name" => $upload->file_name,
                    "size" => (string)$upload->size,
                    "thumbnail" => $upload->thumbnail(null, 150)
                ]
            ]
        ], $transformer->apply($model, "text"));
    }
}

class SomeModelWithMarkdown
{
    public $id;
    public $text;

    public function __construct($id, $text)
    {
        $this->id = $id;
        $this->text = $text;
    }

    public function getKey()
    {
        return $this->id;
    }
}