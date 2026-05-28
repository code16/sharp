<?php

namespace Code16\Sharp\Http\Jobs;

use Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\SharpImageManager;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Encoders\JpegEncoder;
use Spatie\ImageOptimizer\OptimizerChain;
use Spatie\ImageOptimizer\Optimizers\Avifenc;
use Spatie\ImageOptimizer\Optimizers\Pngquant;

class OptimizeImageJob
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public string $disk,
        public string $filePath,
    ) {}

    public function handle(): void
    {
        if ($this->optimizeWithIntervention()) {
            return;
        }

        // We do not need to check for exception nor file format because
        // the package will not throw any errors and just operate silently.
        $chain = app(OptimizerChain::class);

        if ($pngquant = collect($chain->getOptimizers())->whereInstanceOf(Pngquant::class)->first()) {
            if (! collect($pngquant->options)->some(fn ($option) => str_starts_with($option, '--quality'))) {
                $pngquant->options[] = '--quality=85';
            }
        }

        if (! collect($chain->getOptimizers())->whereInstanceOf(Avifenc::class)->first()) {
            $chain->addOptimizer(new Avifenc([
                '-a cq-level=23',
                '-j all',
                '--min 0',
                '--max 63',
                '--minalpha 0',
                '--maxalpha 63',
                '-a end-usage=q',
                '-a tune=ssim',
            ]));
        }

        $chain->optimize(Storage::disk($this->disk)->path($this->filePath));
    }

    protected function optimizeWithIntervention(): bool
    {
        $imageManager = app(SharpImageManager::class);
        $localPath = Storage::disk($this->disk)->path($this->filePath);

        if (Storage::disk($this->disk)->mimeType($this->filePath) === 'image/jpeg'
            && ($exif = $this->getExifData($localPath))
            && ($exif['Orientation'] ?? 1) !== 1
        ) {
            Storage::disk($this->disk)->put(
                $this->filePath,
                $imageManager->read($localPath)->orient()->encode(new JpegEncoder(quality: 85, progressive: true, strip: true)),
            );

            return true;
        }

        return false;
    }

    protected function getExifData(string $path): ?array
    {
        return @exif_read_data($path) ?: null;
    }
}
