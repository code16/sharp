<?php

use Code16\Sharp\Form\Fields\Formatters\EditorFormatter;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Tests\Unit\Form\Fields\Formatters\Fixtures\EditorFormatterTestEmbed;

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
