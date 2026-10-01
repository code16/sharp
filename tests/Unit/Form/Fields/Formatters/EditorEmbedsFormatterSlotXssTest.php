<?php

use Code16\Sharp\Form\Fields\Formatters\EditorFormatter;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Tests\Unit\Form\Fields\Formatters\Fixtures\EditorFormatterTestEmbed;
use Code16\Sharp\Tests\Unit\Form\Fields\Formatters\Fixtures\EditorFormatterTestEmbedWithEditorSlot;
use Code16\Sharp\Tests\Unit\Form\Fields\Formatters\Fixtures\EditorFormatterTestEmbedWithNestedEditorSlot;

it('preserves embeds allowed by nested editor slot fields, at any depth', function () {
    // parent > x-embed-with-nested-editor-slot (slot allows itself) > x-embed-with-editor-slot > x-embed
    $field = SharpFormEditorField::make('body')
        ->allowEmbeds([EditorFormatterTestEmbedWithNestedEditorSlot::class]);

    $result = (new EditorFormatter())->fromFront($field, 'body', [
        'text' => '<x-embed-with-nested-editor-slot data-key="0"></x-embed-with-nested-editor-slot>',
        'embeds' => [
            (new EditorFormatterTestEmbedWithNestedEditorSlot())->key() => [
                '0' => ['slot' => '<x-embed-with-editor-slot onclick="alert(1)"><x-embed check="1">deep <img src=x onerror=alert(1)></x-embed></x-embed-with-editor-slot>'],
            ],
        ],
    ]);

    expect($result)
        ->toContain('<x-embed-with-editor-slot><x-embed check="1">deep <img src="x"></x-embed></x-embed-with-editor-slot>')
        ->not->toContain('onclick')
        ->not->toContain('onerror');
});

it('preserves embeds allowed by an editor slot field but not by the parent editor', function () {
    $field = SharpFormEditorField::make('body')
        ->allowEmbeds([EditorFormatterTestEmbedWithEditorSlot::class]);

    $result = (new EditorFormatter())->fromFront($field, 'body', [
        'text' => '<x-embed-with-editor-slot data-key="0"></x-embed-with-editor-slot>',
        'embeds' => [
            (new EditorFormatterTestEmbedWithEditorSlot())->key() => [
                '0' => ['slot' => '<x-embed check="1" onclick="alert(1)">nested <img src=x onerror=alert(1)></x-embed>'],
            ],
        ],
    ]);

    expect($result)
        ->toContain('<x-embed check="1">nested <img src="x"></x-embed>')
        ->not->toContain('onclick')
        ->not->toContain('onerror');
});

it('sanitizes a raw <img onerror> payload injected through an embed slot field', function () {
    $field = SharpFormEditorField::make('body')
        ->allowEmbeds([EditorFormatterTestEmbed::class]);

    $embedKey = (new EditorFormatterTestEmbed())->key();

    $result = (new EditorFormatter())->fromFront($field, 'body', [
        'text' => '<x-embed data-key="0"></x-embed>',
        'embeds' => [
            $embedKey => [
                '0' => ['slot' => '<img src=x onerror=alert(document.domain)>', 'check' => false],
            ],
        ],
    ]);

    expect($result)->not->toContain('onerror');
});

it('sanitizes a raw <script> payload injected through an embed slot field', function () {
    $field = SharpFormEditorField::make('body')
        ->allowEmbeds([EditorFormatterTestEmbed::class]);

    $embedKey = (new EditorFormatterTestEmbed())->key();

    $result = (new EditorFormatter())->fromFront($field, 'body', [
        'text' => '<x-embed data-key="0"></x-embed>',
        'embeds' => [
            $embedKey => [
                '0' => ['slot' => '<script>alert(document.domain)</script>', 'check' => false],
            ],
        ],
    ]);

    expect($result)->not->toContain('<script');
});

it('sanitizes a raw <img onerror> wrapped in an embed/raw-html marker', function () {
    $field = SharpFormEditorField::make('body')->allowEmbeds([EditorFormatterTestEmbed::class]);
    $embedKey = (new EditorFormatterTestEmbed())->key();
    $result = (new EditorFormatter())->fromFront($field, 'body', [
        'text' => '<x-embed data-key="0"></x-embed>',
        'embeds' => [$embedKey => ['0' => ['slot' => '<x-sharp-image><img src=x onerror=alert(1)></x-sharp-image>', 'check' => false]]],
    ]);
    expect($result)->not->toContain('onerror');
});

it('sanitizes a raw <img onerror> wrapped in <x-sharp-file>', function () {
    $field = SharpFormEditorField::make('body')->allowEmbeds([EditorFormatterTestEmbed::class]);
    $embedKey = (new EditorFormatterTestEmbed())->key();
    $result = (new EditorFormatter())->fromFront($field, 'body', [
        'text' => '<x-embed data-key="0"></x-embed>',
        'embeds' => [$embedKey => ['0' => ['slot' => '<x-sharp-file><img src=x onerror=alert(1)></x-sharp-file>', 'check' => false]]],
    ]);
    expect($result)->not->toContain('onerror');
});

it('sanitizes a raw <img onerror> wrapped in a nested embed tag', function () {
    $field = SharpFormEditorField::make('body')->allowEmbeds([EditorFormatterTestEmbed::class]);
    $embedKey = (new EditorFormatterTestEmbed())->key();
    $result = (new EditorFormatter())->fromFront($field, 'body', [
        'text' => '<x-embed data-key="0"></x-embed>',
        'embeds' => [$embedKey => ['0' => ['slot' => '<x-embed><img src=x onerror=alert(1)></x-embed>', 'check' => false]]],
    ]);
    expect($result)->not->toContain('onerror');
});

it('sanitizes a raw <img onerror> wrapped in a data-html-content marker', function () {
    $field = SharpFormEditorField::make('body')->allowEmbeds([EditorFormatterTestEmbed::class]);
    $embedKey = (new EditorFormatterTestEmbed())->key();
    $result = (new EditorFormatter())->fromFront($field, 'body', [
        'text' => '<x-embed data-key="0"></x-embed>',
        'embeds' => [$embedKey => ['0' => ['slot' => '<div data-html-content="1"><img src=x onerror=alert(1)></div>', 'check' => false]]],
    ]);
    expect($result)->not->toContain('onerror');
});

it('preserves a legitimate <x-sharp-image> upload reference in a slot', function () {
    $field = SharpFormEditorField::make('body')->allowEmbeds([EditorFormatterTestEmbed::class]);
    $embedKey = (new EditorFormatterTestEmbed())->key();
    $result = (new EditorFormatter())->fromFront($field, 'body', [
        'text' => '<x-embed data-key="0"></x-embed>',
        'embeds' => [$embedKey => ['0' => ['slot' => '<x-sharp-image file="{&quot;file_name&quot;:&quot;foo.jpg&quot;}" legend="caption"></x-sharp-image>', 'check' => false]]],
    ]);
    expect($result)
        ->toContain('<x-sharp-image')
        ->and($result)->toContain('legend="caption"');
});
