<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\MarkdownFormatter;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormMarkdownField;
use Code16\Sharp\Tests\SharpTestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class MarkdownFormatterTest extends SharpTestCase
{

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake("local");
        Storage::fake("public");
    }

    /** @test */
    function we_can_format_a_text_value_to_front()
    {
        $formatter = new MarkdownFormatter;
        $field = SharpFormMarkdownField::make("md");
        $value = Str::random() . "\n\n" . Str::random();

        $this->assertEquals(
            [
                "text" => $value, 
            ], 
            $formatter->toFront($field, $value)
        );
    }

    /** @test */
    function we_can_format_a_text_value_from_front()
    {
        $value = Str::random();

        $this->assertEquals(
            $value, 
            (new MarkdownFormatter)->fromFront(
                SharpFormMarkdownField::make("md"), 
                "attribute", 
                ["text" => $value]
            )
        );
    }

    /** @test */
    function we_store_newly_uploaded_files_from_front()
    {
        app()->bind(UploadFormatter::class, function() {
            return new class extends UploadFormatter {
                function fromFront(SharpFormField $field, string $attribute, $value): ?array
                {
                    return [
                        "file_name" => "data/uploaded_test.png",
                        "disk" => "local"
                    ];
                }
            };
        });

        $value = <<<EOT
            Some content text before
            
            <x-sharp-media 
                name="test.png"
                uploaded="true"
            ></x-sharp-media>

            Some content text after
        EOT;
        
        $result = (new MarkdownFormatter)
            ->fromFront(
                SharpFormMarkdownField::make("md")
                    ->setStorageDisk("local")
                    ->setStorageBasePath("data"),
                "attribute",
                [
                    "text" => $value,
                    "files" => [
                        [
                            "name" => "test.png",
                            "uploaded" => true
                        ]
                    ]
                ]
            );
        
        $this->assertStringContainsString(
            "Some content text before",
            $result
        );

        $this->assertStringContainsString(
            "Some content text after",
            $result
        );

        $this->assertStringContainsString(
            '<x-sharp-media name="uploaded_test.png" uploaded="true" path="data/uploaded_test.png" disk="local"></x-sharp-media>',
            $result
        );
    }
}