<?php

use Code16\Sharp\Form\Fields\SharpFormUploadField;
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
            'validation' => [
                'rule' => ['file', 'max:'.config('sharp.uploads.max_file_size') * 1024],
                'allowedExtensions' => [],
                'maximumFileSize' => config('sharp.uploads.max_file_size') * 1024,
            ],
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
        ->setMaxFileSize(3)
        ->setFileFilter('zip');

    expect($formField->toArray()['validation']['rule'])
        ->toEqual(['file', 'extensions:.zip', 'max:3072']);
});

it('allows to define a custom validation rule on an image file', function () {
    $formField = SharpFormUploadField::make('file')
        ->setIsImage()
        ->setMaxFileSize(3)
        ->setImageDimensions(Rule::dimensions()->maxWidth(100)->maxHeight(100));

    expect($formField->toArray()['validation']['rule'])
        ->toEqual(['file', 'max:3072', 'image', 'dimensions:max_width=100,max_height=100']);
});

it('allows to define maxFileSize in the deprecated way', function () {
    $formField = SharpFormUploadField::make('file')
        ->setMaxFileSize(.5);

    expect($formField->toArray()['validation']['rule'])
        ->toEqual(['file', 'max:512']);
});

it('allows to define fileFilter', function () {
    expect(SharpFormUploadField::make('file')->setFileFilter('jpg')->toArray()['validation']['rule'])
        ->toContain('extensions:.jpg')
        ->and(SharpFormUploadField::make('file')->setFileFilter('jpg, gif')->toArray()['validation']['rule'])
        ->toContain('extensions:.jpg,.gif')
        ->and(SharpFormUploadField::make('file')->setFileFilter(['jpg', 'gif '])->toArray()['validation']['rule'])
        ->toContain('extensions:.jpg,.gif');
});
