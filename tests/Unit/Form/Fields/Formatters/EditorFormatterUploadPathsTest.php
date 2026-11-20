<?php

use Code16\Sharp\Form\Fields\Formatters\EditorFormatter;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Tests\Unit\Form\Fields\Formatters\Fixtures\EditorFormatterTestEmbed;
use Code16\Sharp\Tests\Unit\Form\Fields\Formatters\Fixtures\EditorFormatterTestEmbedWithEditorSlot;
use Code16\Sharp\Tests\Unit\Utils\FakesBreadcrumb;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

uses(FakesBreadcrumb::class);

beforeEach(function () {
    Storage::fake('local');
    Storage::fake('public');
});

function unsignedFileJson(): string
{
    return e(json_encode(['file_name' => 'private/secret.pdf', 'disk' => 'local']));
}

function signedFileJson(): string
{
    return e(json_encode([
        'file_name' => 'private/secret.pdf',
        'disk' => 'local',
        'path_signature' => uploadPathSignature('local', 'private/secret.pdf'),
    ]));
}

it('rejects an unsigned upload attribute injected on an embed tag in the editor text', function () {
    $field = SharpFormEditorField::make('md')->allowEmbeds([EditorFormatterTestEmbed::class]);

    (new EditorFormatter())->setInstanceId(1)->fromFront($field, 'md', [
        'text' => sprintf('<x-embed data-key="0" visual="%s"></x-embed>', unsignedFileJson()),
        'embeds' => [
            (new EditorFormatterTestEmbed())->key() => ['0' => ['check' => false]],
        ],
    ]);
})->throws(ValidationException::class);

it('rejects an unsigned file in the editor text, even nested', function () {
    $field = SharpFormEditorField::make('md');

    (new EditorFormatter())->setInstanceId(1)->fromFront($field, 'md', [
        'text' => sprintf('<p><x-sharp-file file="%s"></x-sharp-file></p>', unsignedFileJson()),
    ]);
})->throws(ValidationException::class);

it('rejects an unsigned file in an embed editor slot', function () {
    $field = SharpFormEditorField::make('md')->allowEmbeds([EditorFormatterTestEmbedWithEditorSlot::class]);

    (new EditorFormatter())->setInstanceId(1)->fromFront($field, 'md', [
        'text' => '<x-embed-with-editor-slot data-key="0"></x-embed-with-editor-slot>',
        'embeds' => [
            (new EditorFormatterTestEmbedWithEditorSlot())->key() => [
                '0' => ['slot' => sprintf('<x-sharp-image file="%s"></x-sharp-image>', unsignedFileJson())],
            ],
        ],
    ]);
})->throws(ValidationException::class);

it('rejects an unsigned upload attribute of an embed nested in an embed editor slot', function () {
    $field = SharpFormEditorField::make('md')->allowEmbeds([EditorFormatterTestEmbedWithEditorSlot::class]);

    (new EditorFormatter())->setInstanceId(1)->fromFront($field, 'md', [
        'text' => '<x-embed-with-editor-slot data-key="0"></x-embed-with-editor-slot>',
        'embeds' => [
            (new EditorFormatterTestEmbedWithEditorSlot())->key() => [
                '0' => ['slot' => sprintf('<x-embed visual="%s"></x-embed>', unsignedFileJson())],
            ],
        ],
    ]);
})->throws(ValidationException::class);

it('accepts a signed file in an embed editor slot', function () {
    $field = SharpFormEditorField::make('md')->allowEmbeds([EditorFormatterTestEmbedWithEditorSlot::class]);

    $result = (new EditorFormatter())->setInstanceId(1)->fromFront($field, 'md', [
        'text' => '<x-embed-with-editor-slot data-key="0"></x-embed-with-editor-slot>',
        'embeds' => [
            (new EditorFormatterTestEmbedWithEditorSlot())->key() => [
                '0' => ['slot' => sprintf('<x-sharp-image file="%s"></x-sharp-image>', signedFileJson())],
            ],
        ],
    ]);

    expect($result)->toContain('<x-sharp-image')->toContain('secret.pdf');
});

it('signs files of embed editor slots when formatting to front', function () {
    $this->fakeBreadcrumbWithUrl('/sharp/root/s-list/person/s-show/person/1/s-form/person/1');

    $field = SharpFormEditorField::make('md')->allowEmbeds([EditorFormatterTestEmbedWithEditorSlot::class]);

    $data = (new EditorFormatter())->setInstanceId(1)->toFront(
        $field,
        sprintf(
            '<x-embed-with-editor-slot><x-sharp-image file="%s"></x-sharp-image><x-embed visual="%s"></x-embed></x-embed-with-editor-slot>',
            unsignedFileJson(),
            unsignedFileJson(),
        )
    );

    $slot = $data['embeds'][(new EditorFormatterTestEmbedWithEditorSlot())->key()]->{'0'}['slot'];

    expect(substr_count(html_entity_decode($slot), uploadPathSignature('local', 'private/secret.pdf')))->toBe(2);
});
