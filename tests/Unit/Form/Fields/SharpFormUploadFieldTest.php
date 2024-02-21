<?php

use Code16\Sharp\Form\Fields\SharpFormUploadField;

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
            'shouldOptimizeImage' => false
        ]);
});

it('allows to define maxFileSize', function () {
    $formField = SharpFormUploadField::make('file')
        ->setMaxFileSize(.5);

    expect($formField->toArray())
        ->toHaveKey('maxFileSize', .5);
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

it('allows to define fileFilter', function () {
    $formField = SharpFormUploadField::make('file')
        ->setFileFilter('jpg');

    expect($formField->toArray())
        ->toHaveKey('fileFilter', ['.jpg']);

    $formField = SharpFormUploadField::make('file')
        ->setFileFilter('jpg, gif');

    expect($formField->toArray())
        ->toHaveKey('fileFilter', ['.jpg', '.gif']);

    $formField = SharpFormUploadField::make('file')
        ->setFileFilter(['jpg', 'gif ']);

    expect($formField->toArray())
        ->toHaveKey('fileFilter', ['.jpg', '.gif']);
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
