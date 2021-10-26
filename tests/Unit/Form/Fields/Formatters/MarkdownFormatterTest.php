<?php

namespace Code16\Sharp\Tests\Unit\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\Formatters\EditorFormatter;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
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
        $formatter = new EditorFormatter;
        $field = SharpFormEditorField::make("md");
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
            (new EditorFormatter)->fromFront(
                SharpFormEditorField::make("md"), 
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
                        "file_name" => "data/uploaded_" . $value['name'],
                        "disk" => "local"
                    ];
                }
            };
        });

        $value = <<<EOT
            Some content text before
            
            <x-sharp-file 
                name="test.pdf"
                uploaded="true"
            ></x-sharp-file>
            
            <x-sharp-image 
                name="test.png"
                uploaded="true"
            ></x-sharp-image>

            Some content text after
        EOT;
        
        $result = (new EditorFormatter)
            ->fromFront(
                SharpFormEditorField::make("md")
                    ->setStorageDisk("local")
                    ->setStorageBasePath("data"),
                "attribute",
                [
                    "text" => $value,
                    "files" => [
                        [
                            "name" => "test.pdf",
                            "uploaded" => true
                        ], [
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
            '<x-sharp-file name="uploaded_test.pdf" uploaded="true" path="data/uploaded_test.pdf" disk="local"></x-sharp-file>',
            $result
        );

        $this->assertStringContainsString(
            '<x-sharp-image name="uploaded_test.png" uploaded="true" path="data/uploaded_test.png" disk="local"></x-sharp-image>',
            $result
        );
    }

    /** @test */
    function we_store_newly_uploaded_files_in_a_localized_field_from_front()
    {
        app()->bind(UploadFormatter::class, function() {
            return new class extends UploadFormatter {
                function fromFront(SharpFormField $field, string $attribute, $value): ?array
                {
                    return [
                        "file_name" => "data/uploaded_" . $value['name'],
                        "disk" => "local"
                    ];
                }
            };
        });

        $frValue = <<<EOT
            <x-sharp-file 
                name="test.pdf"
                uploaded="true"
            ></x-sharp-file>

            Some content text after
        EOT;

        $enValue = <<<EOT
            <x-sharp-image 
                name="test.png"
                uploaded="true"
            ></x-sharp-image>

            Some content text after
        EOT;

        $result = (new EditorFormatter)
            ->fromFront(
                SharpFormEditorField::make("md")
                    ->setLocalized()
                    ->setStorageDisk("local")
                    ->setStorageBasePath("data"),
                "attribute",
                [
                    "text" => [
                        "fr" => $frValue,
                        "en" => $enValue
                    ],
                    "files" => [
                        [
                            "name" => "test.pdf",
                            "uploaded" => true
                        ], [
                            "name" => "test.png",
                            "uploaded" => true
                        ]
                    ]
                ]
            );
        
        $this->assertStringContainsString(
            '<x-sharp-file name="uploaded_test.pdf" uploaded="true" path="data/uploaded_test.pdf" disk="local"></x-sharp-file>',
            $result["fr"]
        );

        $this->assertStringContainsString(
            '<x-sharp-image name="uploaded_test.png" uploaded="true" path="data/uploaded_test.png" disk="local"></x-sharp-image>',
            $result["en"]
        );
    }
}