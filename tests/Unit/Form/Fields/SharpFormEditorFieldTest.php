<?php

use Code16\Sharp\Form\Fields\Embeds\SharpFormEditorEmbedUpload;
use Code16\Sharp\Form\Fields\SharpFormEditorField;

it('sets only default values', function () {
    $formField = SharpFormEditorField::make('text');

    expect($formField->toArray())
        ->toEqual([
            'key' => 'text',
            'type' => 'editor',
            'minHeight' => 200,
            'showCharacterCount' => false,
            'toolbar' => [
                SharpFormEditorField::B,
                SharpFormEditorField::I,
                SharpFormEditorField::SEPARATOR,
                SharpFormEditorField::UL,
                SharpFormEditorField::SEPARATOR,
                SharpFormEditorField::A,
            ],
            'embeds' => [
                'upload' => [
//                    'maxFileSize' => 2,
//                    'transformable' => true,
//                    'fileFilter' => ['.jpg', '.jpeg', '.gif', '.png'],
//                    'transformKeepOriginal' => true,
//                    'transformableFileTypes' => null,
//                    'ratioX' => null,
//                    'ratioY' => null,
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

it('allows to allow uploads with configuration', function () {
    $formField = SharpFormEditorField::make('text')
        ->allowUploads(function(SharpFormEditorEmbedUpload $upload) {
            $upload->setFileFilterImages()
                ->setMaxFileSize(50)
                ->setCropRatio('16:9')
                ->setFileFilter(['jpg', 'pdf'])
                ->setHasLegend();
        });

    expect($formField->toArray())
        ->toHaveKey('embeds.upload.maxFileSize', 50)
        ->toHaveKey('embeds.upload.transformable', true)
        ->toHaveKey('embeds.upload.ratioX', 16)
        ->toHaveKey('embeds.upload.ratioY', 9)
        ->toHaveKey('embeds.upload.fileFilter', ['.jpg', '.pdf'])
        ->toHaveKey('embeds.upload.legend', true);

    $formField = SharpFormEditorField::make('text')
        ->allowUploads(function(SharpFormEditorEmbedUpload $upload) {
            $upload->setFileFilterImages()
                ->setTransformable(false);
        });

    expect($formField->toArray())
        ->toHaveKey('embeds.upload.transformable', false);
});

it('allows to define toolbar', function () {
    $formField = SharpFormEditorField::make('text')
        ->setToolbar([
            SharpFormEditorField::TABLE,
            SharpFormEditorField::SEPARATOR,
            SharpFormEditorField::UL,
        ]);

    expect($formField->toArray())
        ->toHaveKey('toolbar', [
            SharpFormEditorField::TABLE,
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
    $formField = SharpFormEditorField::make('text')
        ->setHeight(50)
        ->setRenderContentAsMarkdown();

    expect($formField->toArray())
        ->toHaveKey('markdown', true);
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