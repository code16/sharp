<?php

use Code16\Sharp\Exceptions\Form\SharpFormUpdateException;
use Code16\Sharp\Http\Jobs\HandleUploadedFileJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
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
        disk: 'local',
        filePath: 'data/things/{id}/image.jpg',
        shouldOptimizeImage: false,
        instanceId: 50,
    );

    Storage::disk('local')->assertExists('data/things/50/image.jpg');
});

it('throws if instance id null and {id} in segment', function () {
    UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    HandleUploadedFileJob::dispatch(
        uploadedFileName: 'image.jpg',
        disk: 'local',
        filePath: 'data/things/{id}/image.jpg',
        shouldOptimizeImage: false,
    );
})
    ->throws(SharpFormUpdateException::class);

it('optimizes uploaded images if configured', function () {
    $optimizer = new class()
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

    expect(Storage::disk('local')->size('data/image.jpg'))->not->toEqual($originalSize);
});

it('sanitizes svg files', function () {
    UploadedFile::fake()
        ->createWithContent(
            'image.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert("XSS")</script><rect width="10" height="10"></rect></svg>'
        )
        ->storeAs('/tmp', 'image.svg', ['disk' => 'local']);

    HandleUploadedFileJob::dispatch(
        uploadedFileName: 'image.svg',
        disk: 'local',
        filePath: 'data/image.svg',
        shouldOptimizeImage: false,
        shouldSanitizeSvg: true,
    );

    expect(Storage::disk('local')->get('data/image.svg'))
        ->toEqual('<svg xmlns="http://www.w3.org/2000/svg"><rect width="10" height="10"></rect></svg>');
});

it('does not sanitize svg files if not configured', function () {
    UploadedFile::fake()
        ->createWithContent(
            'image.svg',
            '<svg xmlns="http://www.w3.org/2000/svg"><script>alert("XSS")</script><rect width="10" height="10"></rect></svg>'
        )
        ->storeAs('/tmp', 'image.svg', ['disk' => 'local']);

    HandleUploadedFileJob::dispatch(
        uploadedFileName: 'image.svg',
        disk: 'local',
        filePath: 'data/image.svg',
        shouldOptimizeImage: false,
        shouldSanitizeSvg: false,
    );

    expect(Storage::disk('local')->get('data/image.svg'))
        ->toEqual('<svg xmlns="http://www.w3.org/2000/svg"><script>alert("XSS")</script><rect width="10" height="10"></rect></svg>');
});
