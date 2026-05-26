<?php

use Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\SharpImageManager;
use Code16\Sharp\Http\Jobs\HandleTransformedFileJob;
use Code16\Sharp\Http\Jobs\HandleUploadedFileJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    Storage::fake('local');
});

it('handles image transformations on an existing file if isTransformOriginal is configured', function () {
    $uploadedFile = UploadedFile::fake()->image('image.jpg', 600, 600);
    $originalSize = $uploadedFile->getSize();
    $uploadedFile->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    HandleUploadedFileJob::dispatch(
        uploadedFileName: 'image.jpg',
        disk: 'local',
        filePath: 'data/image.jpg',
        shouldOptimizeImage: false,
        transformFilters: [
            'rotate' => ['angle' => 90],
            'crop' => [
                'x' => 0.5,
                'y' => 0.5,
                'width' => 0.8,
                'height' => 0.8,
            ],
        ],
    );

    $this->assertNotEquals(
        $originalSize,
        Storage::disk('local')->size('data/image.jpg')
    );
});

it('instantiates SharpImageManager with stripMetadata: false', function () {
    UploadedFile::fake()
        ->image('image.jpg', 20, 10)
        ->storeAs('data', 'image.jpg', ['disk' => 'local']);

    $instance = null;
    app()->bind(SharpImageManager::class, function ($app, $parameters) use (&$instance) {
        return $instance = new SharpImageManager(...$parameters);
    });

    HandleTransformedFileJob::dispatchSync(
        disk: 'local',
        filePath: 'data/image.jpg',
        transformFilters: [],
        stripMetadata: false,
    );

    // The inner Intervention ImageManager driver should also have the config set.
    $driverConfig = $instance->driver()->config();
    expect($driverConfig->strip)->toBeFalse()
        ->and($driverConfig->autoOrientation)->toBeTrue();
});

it('instantiates SharpImageManager with stripMetadata: true', function () {
    UploadedFile::fake()
        ->image('image.jpg', 20, 10)
        ->storeAs('data', 'image.jpg', ['disk' => 'local']);

    $instance = null;
    app()->bind(SharpImageManager::class, function ($app, $parameters) use (&$instance) {
        return $instance = new SharpImageManager(...$parameters);
    });

    HandleTransformedFileJob::dispatchSync(
        disk: 'local',
        filePath: 'data/image.jpg',
        transformFilters: [],
        stripMetadata: true,
    );

    // The inner Intervention ImageManager driver should also have the config set.
    $driverConfig = $instance->driver()->config();
    expect($driverConfig->strip)->toBeTrue()
        ->and($driverConfig->autoOrientation)->toBeTrue();
});
