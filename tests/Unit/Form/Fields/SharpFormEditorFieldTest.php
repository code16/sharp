<?php

use Code16\Sharp\Form\Fields\SharpFormEditorField;

it('only_default_values_are_set', function () {
    $formField = SharpFormEditorField::make('text');

    expect($formField->toArray())
        ->toEqual([
            'key' => 'text',
            'type' => 'editor',
            'minHeight' => 200,
            'showCharacterCount' => false,
            'toolbar' => [
                SharpFormEditorField::B, SharpFormEditorField::I, SharpFormEditorField::SEPARATOR,
                SharpFormEditorField::UL,
                SharpFormEditorField::SEPARATOR,
                SharpFormEditorField::A,
            ],
            'embeds' => [
                'upload' => [
                    'maxFileSize' => 2,
                    'transformable' => true,
                    'fileFilter' => ['.jpg', '.jpeg', '.gif', '.png'],
                    'transformKeepOriginal' => true,
                    'transformableFileTypes' => null,
                    'ratioX' => null,
                    'ratioY' => null,
                ],
            ],
            'markdown' => false,
            'inline' => false,
        ]);
});

it('allows to define height', function () {
    $formField = SharpFormEditorField::make('text')
        ->setHeight(50);

    expect($formField->toArray())
        ->toHaveKey('minHeight', 50)
        ->toHaveKey('maxHeight', 50);
});

it('allows to define height with maxHeight', function () {
    $formField = SharpFormEditorField::make('text')
        ->setHeight(50, 100);

    expect($formField->toArray())
        ->toHaveKey('minHeight', 50)
        ->toHaveKey('maxHeight', 100)
        ->and($formField->setHeight(50, 0)->toArray())
        ->toHaveKey('minHeight', 50)
        ->not->toHaveKey('maxHeight');
});

it('allows to define upload configuration', function () {
    $formField = SharpFormEditorField::make('text')
        ->setMaxFileSize(50);

    expect($formField->toArray())
        ->toHaveKey('embeds.upload.maxFileSize', 50)
        ->toHaveKey('embeds.upload.transformable', true);

    $formField->setCropRatio('16:9');

    expect($formField->toArray())
        ->toHaveKey('embeds.upload.maxFileSize', 50)
        ->toHaveKey('embeds.upload.transformable', true)
        ->toHaveKey('embeds.upload.ratioX', 16)
        ->toHaveKey('embeds.upload.ratioY', 9);

    $formField->setFileFilter(['jpg', 'pdf']);

    expect($formField->toArray())
        ->toHaveKey('embeds.upload.maxFileSize', 50)
        ->toHaveKey('embeds.upload.transformable', true)
        ->toHaveKey('embeds.upload.ratioX', 16)
        ->toHaveKey('embeds.upload.ratioY', 9)
        ->toHaveKey('embeds.upload.fileFilter', ['.jpg', '.pdf']);

    $formField->setTransformable(false);

    expect($formField->toArray())
        ->toHaveKey('embeds.upload.maxFileSize', 50)
        ->toHaveKey('embeds.upload.transformable', false)
        ->toHaveKey('embeds.upload.ratioX', 16)
        ->toHaveKey('embeds.upload.ratioY', 9)
        ->toHaveKey('embeds.upload.fileFilter', ['.jpg', '.pdf']);
});

it('allows to define toolbar', function () {
    $formField = SharpFormEditorField::make('text')
        ->setToolbar([
            SharpFormEditorField::UPLOAD,
            SharpFormEditorField::SEPARATOR,
            SharpFormEditorField::UL,
        ]);

    expect($formField->toArray())
        ->toHaveKey('toolbar', [
            SharpFormEditorField::UPLOAD,
            SharpFormEditorField::SEPARATOR,
            SharpFormEditorField::UL,
        ]);
});

it('allows to hide toolbar', function () {
    $formField = SharpFormEditorField::make('text')
        ->setHeight(50)
        ->hideToolbar();

    expect($formField->toArray())
        ->not->toHaveKey('toolbar');
});

it('allows to define markdown as content renderer', function () {
    // These configs are globally set in the config
    config()->set('sharp.markdown_editor', [
        'tight_lists_only' => true,
        'nl2br' => true,
    ]);

    $formField = SharpFormEditorField::make('text')
        ->setHeight(50)
        ->setRenderContentAsMarkdown();

    expect($formField->toArray())
        ->toHaveKey('markdown', true)
        ->toHaveKey('tightListsOnly', true)
        ->toHaveKey('nl2br', true);
});

it('allows to define setWithoutParagraphs', function () {
    $formField = SharpFormEditorField::make('text')
        ->setWithoutParagraphs();

    expect($formField->toArray())
        ->toHaveKey('inline', true);
});

it('allows to define maxLength and showCount', function () {
    expect(SharpFormEditorField::make('text')->setMaxLength(500)->toArray())
        ->toHaveKey('maxLength', 500)
        ->toHaveKey('showCharacterCount', true)
        ->and(SharpFormEditorField::make('text')->showCharacterCount()->toArray())
        ->toHaveKey('showCharacterCount', true);
});