<?php

use Code16\Sharp\Http\Jobs\OptimizeImageJob;
use Illuminate\Http\UploadedFile;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\Avifenc;
use Spatie\ImageOptimizer\Optimizers\Jpegoptim;

it('optimizes uploaded images if configured', function () {
    app()->singleton(OptimizerChain::class, fn () => new class() extends OptimizerChain
    {
        public bool $optimizedPathToImage = false;

        public function __construct()
        {
            parent::__construct();

            foreach (config('image-optimizer.optimizers') as $optimizer => $optimizerConfig) {
                $this->addOptimizer(new $optimizer($optimizerConfig));
            }
        }

        public function optimize(string $pathToImage, ?string $pathToOutput = null): bool
        {
            $this->optimizedPathToImage = true;

            return true;
        }
    });

    $path = UploadedFile::fake()
        ->image('image.jpg')
        ->storeAs('/tmp', 'image.jpg', ['disk' => 'local']);

    OptimizeImageJob::dispatch(
        disk: 'local',
        filePath: 'data/image.jpg',
    );

    expect(collect(app(OptimizerChain::class)->getOptimizers())->whereInstanceOf(Jpegoptim::class)->first()->options)
        ->toContain('--keep-exif');
    expect(collect(app(OptimizerChain::class)->getOptimizers())->whereInstanceOf(Avifenc::class))
        ->not->toBeEmpty();

    expect(app(OptimizerChain::class)->optimizedPathToImage)->toEqual($path);
});
