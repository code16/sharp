<?php

use Code16\Sharp\Http\Jobs\HandleUploadedFileJob;
use Illuminate\Http\UploadedFile;
use Spatie\ImageOptimizer\OptimizerChainFactory;

beforeEach(function () {
    Storage::fake('local');
});

it('moves a newly uploaded file in the correct folder', function () {
    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    HandleUploadedFileJob::dispatch(
        uploadedFileName: 'image.jpg',
        disk: 'local',
        filePath: 'data/image.jpg',
        shouldOptimizeImage: false,
    );

    Storage::disk('local')->assertExists('data/image.jpg');
});

it('handles the {id} segment of the file path', function () {
    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    HandleUploadedFileJob::dispatch(
        uploadedFileName: 'image.jpg',
        filePath: 'data/things/{id}/image.jpg',
        disk: 'local',
        instanceId: 50,
        shouldOptimizeImage: false,
    );

    Storage::disk('local')->assertExists('data/things/50/image.jpg');
});

it('optimizes uploaded images if configured', function () {
    $optimizer = new class
    {
        public bool $wasOptimized = false;

        public function optimize(): bool
        {
            $this->wasOptimized = true;

            return true;
        }
    };

    app()->bind(OptimizerChainFactory::class, fn () => new class($optimizer)
    {
        private $optimizer;

        public function __construct(&$optimizer)
        {
            $this->optimizer = $optimizer;
        }

        public function create()
        {
            return $this->optimizer;
        }
    });

    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    HandleUploadedFileJob::dispatch(
        uploadedFileName: 'image.jpg',
        disk: 'local',
        filePath: 'data/image.jpg',
        shouldOptimizeImage: true,
    );

    Storage::disk('local')->assertExists('data/image.jpg');
    expect($optimizer->wasOptimized)->toBeTrue();
});

it('handles image transformations on a newly uploaded file if isTransformOriginal is configured', function () {
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
