<?php

use Code16\Sharp\Http\Jobs\HandleUploadedFileJob;
use Illuminate\Http\UploadedFile;

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
