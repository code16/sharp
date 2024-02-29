<?php

use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Utils\Fields\Validation\SharpFileValidation;
use Code16\Sharp\Utils\Fields\Validation\SharpImageValidation;
use Illuminate\Validation\Rule;

it('sets only default values', function () {
    $formField = SharpFormUploadField::make('file');

    expect($formField->toArray())
        ->toEqual([
            'key' => 'file',
            'type' => 'upload',
            'compactThumbnail' => false,
            'transformable' => true,
            'transformKeepOriginal' => true,
            'storageBasePath' => 'data',
            'storageDisk' => 'local',
            'shouldOptimizeImage' => false,
            'rule' => ['file']
        ]);
});

it('allows to define compactThumbnail', function () {
    $formField = SharpFormUploadField::make('file')
        ->setCompactThumbnail();

    expect($formField->toArray())
        ->toHaveKey('compactThumbnail', true);
});

it('allows to define transformable', function () {
    $formField = SharpFormUploadField::make('file')
        ->setTransformable(false);

    expect($formField->toArray())
        ->toHaveKey('transformable', false);
});

it('allows to define transformKeepOriginal with transformable', function () {
    $formField = SharpFormUploadField::make('file')
        ->setTransformable(true, false);

    expect($formField->toArray())
        ->toHaveKey('transformKeepOriginal', false)
        ->toHaveKey('transformable', true);
});

it('allows to define transformKeepOriginal with config', function () {
    config()->set('sharp.uploads.transform_keep_original_image', false);

    $formField = SharpFormUploadField::make('file');

    expect($formField->toArray())
        ->toHaveKey('transformKeepOriginal', false)
        ->toHaveKey('transformable', true);
});

it('allows to define cropRatio', function () {
    $formField = SharpFormUploadField::make('file')
        ->setCropRatio('16:9');

    expect($formField->toArray())
        ->toHaveKey('ratioX', 16)
        ->toHaveKey('ratioY', 9);
});

it('allows to define transformableFileTypes', function () {
    $formField = SharpFormUploadField::make('file')
        ->setCropRatio('16:9', ['jpg', 'jpeg']);

    expect($formField->toArray())
        ->toHaveKey('transformableFileTypes', ['.jpg', '.jpeg'])
        ->toHaveKey('ratioX', 16)
        ->toHaveKey('ratioY', 9);

    $formField = SharpFormUploadField::make('file')
        ->setCropRatio('16:9', ['.jpg', '.jpeg']);

    expect($formField->toArray())
        ->toHaveKey('transformableFileTypes', ['.jpg', '.jpeg'])
        ->toHaveKey('ratioX', 16)
        ->toHaveKey('ratioY', 9);
});

it('allows to define shouldOptimizeImage', function () {
    $formField = SharpFormUploadField::make('file')->shouldOptimizeImage();
    expect($formField->isShouldOptimizeImage())->toBeTrue();

    $formField2 = SharpFormUploadField::make('file')->shouldOptimizeImage(false);
    expect($formField2->isShouldOptimizeImage())->toBeFalse();
});

it('allows to define a custom validation rule on a file', function () {
    $formField = SharpFormUploadField::make('file')
        ->setValidationRule(
            SharpFileValidation::make()
                ->max('3mb')
        );

    expect($formField->toArray()['rule'])
        ->toEqual(['file', 'max:3000']);
});

it('allows to define a custom validation rule on an image file', function () {
    $formField = SharpFormUploadField::make('file')
        ->setValidationRule(
            SharpImageValidation::make()
                ->dimensions(Rule::dimensions()->maxWidth(100)->maxHeight(100))
                ->max('3mb')
        );

    expect($formField->toArray()['rule'])
        ->toEqual(['file', 'max:3000', 'image', 'dimensions:max_width=100,max_height=100']);
});

it('allows to define maxFileSize in the deprecated way', function () {
    $formField = SharpFormUploadField::make('file')
        ->setMaxFileSize(.5);

    expect($formField->toArray()['rule'])
        ->toEqual(['file', 'max:512']);
});

it('allows to define fileFilter in the deprecated way', function () {
    $formField = SharpFormUploadField::make('file')
        ->setFileFilter('jpg');

    expect($formField->toArray()['rule'])
        ->toEqual(['file', 'extensions:.jpg']);

    $formField = SharpFormUploadField::make('file')
        ->setFileFilter('jpg, gif');

    expect($formField->toArray()['rule'])
        ->toEqual(['file', 'extensions:.jpg,.gif']);

    $formField = SharpFormUploadField::make('file')
        ->setFileFilter(['jpg', 'gif ']);

    expect($formField->toArray()['rule'])
        ->toEqual(['file', 'extensions:.jpg,.gif']);
});