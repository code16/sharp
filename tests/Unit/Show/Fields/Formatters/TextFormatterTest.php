<?php

use Code16\Sharp\Show\Fields\Formatters\TextFieldFormatter;
use Code16\Sharp\Show\Fields\SharpShowTextField;
use Code16\Sharp\Tests\Unit\Form\Fields\Formatters\Fixtures\EditorFormatterTestEmbed;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

it('allows to format a text value to front', function () {
    $formatter = new TextFieldFormatter();
    $field = SharpShowTextField::make('md');
    $value = Str::random()."\n\n".Str::random();

    $this->assertEquals(
        [
            'text' => $value,
        ],
        $formatter->toFront($field, $value),
    );
});

it('allows to format a text with uploads to front', function () {
    $formatter = new TextFieldFormatter();
    $field = SharpShowTextField::make('md');

    $image = UploadedFile::fake()->image('test.jpg', 600, 600);
    $image->storeAs('data/Posts/1', 'image.jpg', ['disk' => 'local']);
    $file = UploadedFile::fake()->create('doc.pdf');
    $file->storeAs('data/Posts/1', 'doc.pdf', ['disk' => 'local']);

    $value = sprintf('<x-sharp-image file="%s" legend="Legendary"></x-sharp-image><x-sharp-file file="%s"></x-sharp-file>',
        e(json_encode([
            'file_name' => 'data/Posts/1/image.jpg',
            'size' => 120,
            'mime_type' => 'image/jpeg',
            'disk' => 'local',
        ])),
        e(json_encode([
            'file_name' => 'data/Posts/1/doc.pdf',
            'size' => 120,
            'mime_type' => 'application/pdf',
            'disk' => 'local',
        ]))
    );

    expect($formatter->toFront($field, $value))->toEqual([
        'text' => '<x-sharp-image data-key="0"></x-sharp-image><x-sharp-file data-key="1"></x-sharp-file>',
        'uploads' => [
            [
                'file' => [
                    'name' => 'image.jpg',
                    'path' => 'data/Posts/1/image.jpg',
                    'disk' => 'local',
                    'thumbnail' => sprintf(
                        '/storage/thumbnails/data/Posts/1/200-200_q-90/image.jpg?%s',
                        Storage::disk('public')->lastModified('/thumbnails/data/Posts/1/200-200_q-90/image.jpg')
                    ),
                    'size' => 120,
                    'mime_type' => 'image/jpeg',
                    'filters' => null,
                    'id' => null,
                ],
                'legend' => 'Legendary',
            ],
            [
                'file' => [
                    'name' => 'doc.pdf',
                    'path' => 'data/Posts/1/doc.pdf',
                    'disk' => 'local',
                    'thumbnail' => null,
                    'size' => 120,
                    'mime_type' => 'application/pdf',
                    'filters' => null,
                    'id' => null,
                ],
                'legend' => null,
            ],
        ],
    ]);
});

it('allows to format embeds with uploads to front', function () {
    $formatter = (new TextFieldFormatter);
    $field = SharpShowTextField::make('md')
        ->allowEmbeds([EditorFormatterTestEmbed::class]);

    expect($formatter->toFront($field, <<<'HTML'
        <x-embed>My <em>contentful</em> content</x-embed>
        HTML
    ))->toEqual([
        'text' => <<<'HTML'
            <x-embed data-key="0"></x-embed>
            HTML,
        'embeds' => [
            (new EditorFormatterTestEmbed())->key() => [
                [
                    'slot' => 'My <em>contentful</em> content',
                ],
            ],
        ],
    ]);
});

