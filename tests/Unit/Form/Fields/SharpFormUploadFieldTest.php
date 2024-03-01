<?php

use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Illuminate\Validation\Rule;

it('sets only default values', function () {
    $formField = SharpFormUploadField::make('file');

    expect($formField->toArray())
        ->toEqual([
            'key' => 'file',
            'type' => 'upload',
            'imageCompactThumbnail' => false,
            'imageTransformable' => true,
            'imageTransformKeepOriginal' => true,
            'storageBasePath' => 'data',
            'storageDisk' => 'local',
            'maxFileSize' => config('sharp.uploads.max_file_size'),
            'validationRule' => ['file', 'max:'.config('sharp.uploads.max_file_size') * 1024],
        ]);
});

it('allows to define compactThumbnail', function () {
    $formField = SharpFormUploadField::make('file')
        ->setImageCompactThumbnail();

    expect($formField->toArray())
        ->toHaveKey('imageCompactThumbnail', true);
});

it('allows to define transformable', function () {
    $formField = SharpFormUploadField::make('file')
        ->setImageTransformable(false);

    expect($formField->toArray())
        ->toHaveKey('imageTransformable', false);
});

it('allows to define transformKeepOriginal with transformable', function () {
    $formField = SharpFormUploadField::make('file')
        ->setImageTransformable(true, false);

    expect($formField->toArray())
        ->toHaveKey('imageTransformKeepOriginal', false)
        ->toHaveKey('imageTransformable', true);
});

it('allows to define transformKeepOriginal with config', function () {
    config()->set('sharp.uploads.transform_keep_original_image', false);

    $formField = SharpFormUploadField::make('file');

    expect($formField->toArray())
        ->toHaveKey('imageTransformKeepOriginal', false)
        ->toHaveKey('imageTransformable', true);
});

it('allows to define cropRatio', function () {
    $formField = SharpFormUploadField::make('file')
        ->setImageCropRatio('16:9');

    expect($formField->toArray())
        ->toHaveKey('imageCropRatio', [16, 9]);
});

it('allows to define transformableFileTypes', function () {
    $formField = SharpFormUploadField::make('file')
        ->setImageCropRatio('16:9', ['jpg', 'jpeg']);

    expect($formField->toArray())
        ->toHaveKey('imageTransformableFileTypes', ['.jpg', '.jpeg'])
        ->toHaveKey('imageCropRatio', [16, 9]);

    $formField = SharpFormUploadField::make('file')
        ->setImageCropRatio('16:9', ['.jpg', '.jpeg']);

    expect($formField->toArray())
        ->toHaveKey('imageTransformableFileTypes', ['.jpg', '.jpeg'])
        ->toHaveKey('imageCropRatio', [16, 9]);
});

it('allows to define shouldOptimizeImage', function () {
    $formField = SharpFormUploadField::make('file')->setImageOptimize();
    expect($formField->isImageOptimize())->toBeTrue();

    $formField2 = SharpFormUploadField::make('file')->setImageOptimize(false);
    expect($formField2->isImageOptimize())->toBeFalse();
});

it('allows to define a custom validation rule on a file', function () {
    $formField = SharpFormUploadField::make('file')
        ->setMaxFileSize(3)
        ->setAllowedExtensions('zip');

    expect($formField->toArray()['validationRule'])
        ->toEqual(['file', 'extensions:zip', 'max:3072']);
});

it('allows to define a custom validation rule on an image file', function () {
    $formField = SharpFormUploadField::make('file')
        ->setImageOnly()
        ->setMaxFileSize(3)
        ->setImageDimensionConstraints(Rule::dimensions()->maxWidth(100)->maxHeight(100));

    expect($formField->toArray()['validationRule'])
        ->toEqual([
            'file',
            'extensions:jpg,jpeg,png,gif,bmp,svg,webp',
            'max:3072',
            'image',
            'dimensions:max_width=100,max_height=100',
        ]);
});

it('allows to define maxFileSize in the deprecated way', function () {
    $formField = SharpFormUploadField::make('file')
        ->setMaxFileSize(.5);

    expect($formField->toArray()['validationRule'])
        ->toEqual(['file', 'max:512']);
});

it('allows to define fileFilter', function () {
    $field = SharpFormUploadField::make('file')->setAllowedExtensions('jpg');
    expect($field->toArray()['validationRule'])->toContain('extensions:jpg')
        ->and($field->toArray()['allowedExtensions'])->toEqual(['.jpg']);
    
    $field = SharpFormUploadField::make('file')->setAllowedExtensions('jpg, gif');
    expect($field->toArray()['validationRule'])->toContain('extensions:jpg,gif')
        ->and($field->toArray()['allowedExtensions'])->toEqual(['.jpg', '.gif']);
    
    $field = SharpFormUploadField::make('file')->setAllowedExtensions(['jpg', 'gif ']);
    expect($field->toArray()['validationRule'])->toContain('extensions:jpg,gif')
        ->and($field->toArray()['allowedExtensions'])->toEqual(['.jpg', '.gif']);
});
