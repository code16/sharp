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

        Storage::fake('local');
    }

    /** @test */
    public function we_can_format_a_text_value_to_front()
    {
        $formatter = new MarkdownFormatter();
        $field = SharpFormMarkdownField::make('md');
        $value = Str::random();

        $this->assertEquals(['text' => $value], $formatter->toFront($field, $value));
    }

    /** @test */
    public function when_text_has_an_embedded_upload_the_files_array_is_handled_to_front()
    {
        UploadedFile::fake()
            ->image('test.png')
            ->storeAs('data', 'test.png', 'local');

        $formatter = new MarkdownFormatter();
        $field = SharpFormMarkdownField::make('md');
        $value = '![](local:data/test.png)';

        $this->assertEquals(
            'local:data/test.png',
            $formatter->toFront($field, $value)['files'][0]['name'],
        );
    }

    /** @test */
    public function when_text_has_multiple_embedded_uploads_the_files_array_is_handled_to_front()
    {
        UploadedFile::fake()->image('test.png')->storeAs('data', 'test.png', 'local');
        UploadedFile::fake()->image('test2.png')->storeAs('data', 'test2.png', 'local');
        UploadedFile::fake()->image('test3.png')->storeAs('data', 'test3.png', 'local');

        $formatter = new MarkdownFormatter();
        $field = SharpFormMarkdownField::make('md');
        $value = "![](local:data/test.png)\n![](local:data/test2.png)\n![](local:data/test3.png)";

        $this->assertCount(3, $formatter->toFront($field, $value)['files']);
    }

    /** @test */
    public function we_send_the_file_size_and_thumbnail_to_front()
    {
        UploadedFile::fake()->image('test.png', 600, 600)->storeAs('data', 'test.png', 'local');

        $formatter = new MarkdownFormatter();
        $field = SharpFormMarkdownField::make('md');
        $value = '![](local:data/test.png)';

        $toFrontArray = $formatter->toFront($field, $value)['files'][0];

        $this->assertEquals('local:data/test.png', $toFrontArray['name']);
        $this->assertTrue($toFrontArray['size'] > 0);
        $this->assertStringStartsWith('/storage/thumbnails/data/1000-400/test.png', $toFrontArray['thumbnail']);
    }

    /** @test */
    public function we_can_format_a_text_value_from_front()
    {
        $formatter = new MarkdownFormatter();
        $field = SharpFormMarkdownField::make('md');
        $value = Str::random();
        $attribute = 'attribute';

        $this->assertEquals($value, $formatter->fromFront($field, $attribute, ['text' => $value]));
    }

    /** @test */
    public function we_store_newly_uploaded_files_from_front()
    {
        $formatter = new MarkdownFormatter();
        $field = SharpFormMarkdownField::make('md');
        $value = '![](local:new_test.png)';
        $attribute = 'attribute';

        app()->bind(UploadFormatter::class, function () {
            return new class() extends UploadFormatter
            {
                public function fromFront(SharpFormField $field, string $attribute, $value)
                {
                    return [
                        'file_name' => 'uploaded_test.png',
                    ];
                }
            };
        });

        $this->assertEquals(
            '![](local:uploaded_test.png)',
            $formatter->fromFront($field, $attribute, [
                'text' => $value,
                'files' => [[
                    'name' => 'local:new_test.png',
                    'uploaded' => true,
                ]],
            ]),
        );
    }

    /** @test */
    public function files_are_handled_for_a_localized_markdown()
    {
        $formatter = new MarkdownFormatter();
        $field = SharpFormMarkdownField::make('md')->setLocalized();
        $value = [
            'fr' => "![](local:test_fr.png)\n![](local:test2_fr.png)",
            'en' => '![](local:test_en.png)',
        ];

        $this->assertCount(3, $formatter->toFront($field, $value)['files']);
    }

    /** @test */
    public function we_apply_transformations_from_front_on_already_existing_files()
    {
        UploadedFile::fake()->image('image.png', 100, 100)->storeAs('data/Test', 'image.png', 'local');

        // We create an implementation where deleteThumbnails() is faked
        // in order to check that it's called without changing anything
        // else in the class. The fact that deleteThumbnails() is called
        // is a proof that the image was transformed.
        $formatter = new class() extends MarkdownFormatter
        {
            public $thumbnailsDeleted = false;

            protected function deleteThumbnails(string $fullFileName): void
            {
                $this->thumbnailsDeleted = true;
            }
        };

        $field = SharpFormMarkdownField::make('md')
            ->setStorageDisk('local')
            ->setStorageBasePath('data/Test');

        $this->assertEquals(
            '![](local:data/Test/image.png)',
            $formatter->fromFront($field, 'attribute', [
                'text' => '![](local:data/Test/image.png)',
                'files' => [[
                    'name' => 'local:data/Test/image.png',
                    'uploaded' => false,
                    'cropData' => [
                        'height' => .8, 'width' => .6, 'x' => 0, 'y' => .1, 'rotate' => 0,
                    ],
                ]],
            ]),
        );

        $this->assertTrue($formatter->thumbnailsDeleted);
    }

    /** @test */
    public function we_ensure_that_files_are_formatted_in_their_own_paragraph()
    {
        $formatter = new MarkdownFormatter();
        $field = SharpFormMarkdownField::make('md');

        $this->assertEquals(
            "before file\n\n![](local:test.png)\n\nafter file with a [link](http://www.google.fr)",
            $formatter->fromFront($field, 'attribute', [
                'text' => "before file\n![](local:test.png)\nafter file with a [link](http://www.google.fr)",
                'files' => [[
                    'name' => 'local:test.png',
                    'uploaded' => false,
                ]],
            ]),
        );
    }
}
