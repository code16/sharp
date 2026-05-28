<?php

use Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\SharpImageManager;
use Code16\Sharp\Http\Jobs\OptimizeImageJob;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Interfaces\EncodedImageInterface;
use Intervention\Image\Interfaces\ImageInterface;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\Avifenc;

it('optimizes uploaded images if configured', function () {
    app()->singleton(OptimizerChain::class, fn () => new class() extends OptimizerChain
    {
        public ?string $optimizedPathToImage = null;

        public function __construct()
        {
            parent::__construct();

            foreach (config('image-optimizer.optimizers') as $optimizer => $optimizerConfig) {
                $this->addOptimizer(new $optimizer($optimizerConfig));
            }
        }

        public function optimize(string $pathToImage, ?string $pathToOutput = null): bool
        {
            $this->optimizedPathToImage = $pathToImage;

            return true;
        }
    });

    Storage::fake('local');
    $path = UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('data', 'image.jpg', ['disk' => 'local']);

    OptimizeImageJob::dispatch(
        disk: 'local',
        filePath: 'data/image.jpg',
    );

    expect(collect(app(OptimizerChain::class)->getOptimizers())->whereInstanceOf(Avifenc::class))
        ->not->toBeEmpty();

    expect(app(OptimizerChain::class)->optimizedPathToImage)->toEqual(Storage::disk('local')->path($path));
});

it('rotates image with intervention if it has orientation EXIF tag', function () {
    $imageManager = Mockery::mock(SharpImageManager::class);
    $image = Mockery::mock(ImageInterface::class);
    $encodedImage = Mockery::mock(EncodedImageInterface::class);

    app()->bind(SharpImageManager::class, fn () => $imageManager);

    $optimizerChain = new class() extends OptimizerChain
    {
        public bool $optimized = false;

        public function optimize(string $pathToImage, ?string $pathToOutput = null): bool
        {
            $this->optimized = true;

            return true;
        }
    };
    app()->singleton(OptimizerChain::class, fn () => $optimizerChain);

    Storage::fake('local');
    $path = UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('data', 'image.jpg', ['disk' => 'local']);
    $localPath = Storage::disk('local')->path($path);

    $imageManager->shouldReceive('read')
        ->with($localPath)
        ->andReturn($image);

    $image->shouldReceive('orient')
        ->andReturnSelf();

    $image->shouldReceive('encode')
        ->andReturn($encodedImage);

    $encodedImage->shouldReceive('__toString')
        ->andReturn('fake-image-content');

    $job = Mockery::mock(OptimizeImageJob::class, ['local', 'data/image.jpg'])
        ->makePartial()
        ->shouldAllowMockingProtectedMethods();

    $job->shouldReceive('getExifData')
        ->with($localPath)
        ->andReturn(['Orientation' => 3]);

    $job->handle();

    Storage::disk('local')->assertExists('data/image.jpg');
    expect(Storage::disk('local')->get('data/image.jpg'))->toBe('fake-image-content');
    expect($optimizerChain->optimized)->toBeFalse();
});
