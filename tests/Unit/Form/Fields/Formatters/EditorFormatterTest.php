<?php

use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\Formatters\EditorFormatter;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Tests\Unit\Form\Fields\Formatters\Fixtures\EditorFormatterTestEmbed;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

it('allows to format a text value to front', function () {
    $formatter = new EditorFormatter();
    $field = SharpFormEditorField::make('md');
    $value = Str::random()."\n\n".Str::random();

    $this->assertEquals(
        [
            'text' => $value,
        ],
        $formatter->toFront($field, $value),
    );
});

it('allows to format a text value from front', function () {
    $value = Str::random();

    $this->assertEquals(
        $value,
        (new EditorFormatter())->fromFront(
            SharpFormEditorField::make('md'),
            'attribute',
            ['text' => $value],
        ),
    );
});

it('allows to format a text with uploads to front', function () {
    $formatter = new EditorFormatter();
    $field = SharpFormEditorField::make('md')
        ->allowUploads(SharpFormEditorUpload::make());

    UploadedFile::fake()->image('test.jpg', 600, 600)
        ->storeAs('data/Posts/1', 'image.jpg', ['disk' => 'local']);
    UploadedFile::fake()->create('doc.pdf')
        ->storeAs('data/Posts/1', 'doc.pdf', ['disk' => 'local']);

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
        'uploads' => (object) [
            '0' => [
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

it('allows to format text with uploads from front', function () {
    $formatter = (new EditorFormatter())->setInstanceId(1);
    $field = SharpFormEditorField::make('md')
        ->allowUploads(SharpFormEditorUpload::make()->setStorageBasePath('data/Posts/{id}'));

    UploadedFile::fake()->image('uploaded.jpg', 600, 600)
        ->storeAs('/tmp', 'uploaded.jpg', ['disk' => 'local']);

    expect($formatter->fromFront($field, 'attribute', [
        'text' => <<<'HTML'
            <x-sharp-image data-key="0"></x-sharp-image>
            <x-sharp-image data-key="1"></x-sharp-image>
            <x-sharp-file data-key="2"></x-sharp-file>
            HTML,
        'uploads' => [
            [
                'file' => [
                    'name' => 'uploaded.jpg',
                    'uploaded' => true,
                ],
                'legend' => 'Legendary',
            ],
            [
                'file' => [
                    'name' => 'transformed.jpg',
                    'path' => 'data/Posts/1/transformed.jpg',
                    'mime_type' => 'image/jpeg',
                    'disk' => 'local',
                    'size' => 120,
                    'filters' => ['rotate' => ['angle' => 90]],
                    'transformed' => true,
                ],
            ],
            [
                'file' => [
                    'name' => 'doc.pdf',
                    'path' => 'data/Posts/1/doc.pdf',
                    'mime_type' => 'application/pdf',
                    'disk' => 'local',
                    'size' => 120,
                ],
            ],
        ],
    ]))->toEqual(sprintf(<<<'HTML'
        <x-sharp-image file="%s" legend="Legendary"></x-sharp-image>
        <x-sharp-image file="%s"></x-sharp-image>
        <x-sharp-file file="%s"></x-sharp-file>
        HTML,
        e(json_encode([
            'file_name' => 'data/Posts/1/uploaded.jpg',
            'size' => 6467,
            'mime_type' => 'image/jpeg',
            'disk' => 'local',
        ])),
        e(json_encode([
            'file_name' => 'data/Posts/1/transformed.jpg',
            'size' => 120,
            'mime_type' => 'image/jpeg',
            'disk' => 'local',
            'filters' => ['rotate' => ['angle' => 90]],
        ])),
        e(json_encode([
            'file_name' => 'data/Posts/1/doc.pdf',
            'size' => 120,
            'mime_type' => 'application/pdf',
            'disk' => 'local',
        ]))
    ));
});

it('allows to format embeds with uploads to front', function () {
    $formatter = (new EditorFormatter())->setInstanceId(1);
    $field = SharpFormEditorField::make('md')
        ->allowEmbeds([EditorFormatterTestEmbed::class]);

    $image = UploadedFile::fake()->image('test.jpg', 600, 600);
    $image->storeAs('data/Posts/1', 'image.jpg', ['disk' => 'local']);

    $value = sprintf(<<<'HTML'
        <x-embed visual="%s">My <em>contentful</em> content</x-embed>
        HTML,
        e(json_encode([
            'file_name' => 'data/Posts/1/image.jpg',
            'size' => 120,
            'mime_type' => 'image/jpeg',
            'disk' => 'local',
        ]))
    );
    
    
    $data = $formatter->toFront($field, $value);
    $thumbnail = sprintf(
        '/storage/thumbnails/data/Posts/1/200-200_q-90/image.jpg?%s',
        Storage::disk('public')->lastModified('/thumbnails/data/Posts/1/200-200_q-90/image.jpg')
    );

    expect($data)->toEqual([
        'text' => <<<'HTML'
            <x-embed data-key="0"></x-embed>
            HTML,
        'embeds' => [
            (new EditorFormatterTestEmbed())->key() => (object) [
                '0' => [
                    'slot' => 'My <em>contentful</em> content',
                    'visual' => [
                        'name' => 'image.jpg',
                        'path' => 'data/Posts/1/image.jpg',
                        'disk' => 'local',
                        'thumbnail' => $thumbnail,
                        'size' => 120,
                        'mime_type' => 'image/jpeg',
                        'filters' => null,
                        'id' => null,
                    ],
                    '_html' => sprintf('<img src="%s"> My <em>contentful</em> content',
                        $thumbnail,
                    ),
                ],
            ],
        ],
    ]);
});

it('allows to format embeds with uploads from front', function () {
    $formatter = (new EditorFormatter())->setInstanceId(1);
    $field = SharpFormEditorField::make('md')
        ->allowEmbeds([EditorFormatterTestEmbed::class]);

    expect($formatter->fromFront($field, 'attribute', [
        'text' => <<<'HTML'
            <x-embed data-key="0"></x-embed>
            HTML,
        'embeds' => [
            (new EditorFormatterTestEmbed())->key() => [
                '0' => [
                    'slot' => 'My <em>contentful</em> content',
                    'visual' => [
                        'name' => 'image.jpg',
                        'path' => 'data/Posts/1/image.jpg',
                        'disk' => 'local',
                        'thumbnail' => 'thumbnail.jpg',
                        'size' => 120,
                        'mime_type' => 'image/jpeg',
                        'filters' => null,
                        'id' => null,
                    ],
                ],
            ],
        ],
    ]))->toEqual(sprintf('<x-embed visual="%s">My <em>contentful</em> content</x-embed>',
        e(json_encode([
            'file_name' => 'data/Posts/1/image.jpg',
            'size' => 120,
            'mime_type' => 'image/jpeg',
            'disk' => 'local',
        ]))
    ));
});

it('allows to format a unicode text value from front', function () {
    // This test was created to demonstrate preg_replace failure
    // without the unicode modifier
    $value = '<p>ąężółść</p>';

    $this->assertEquals(
        $value,
        (new EditorFormatter())->fromFront(
            SharpFormEditorField::make('md'),
            'attribute',
            ['text' => $value],
        ),
    );
});
