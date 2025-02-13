<?php

namespace Code16\Sharp\Http\Jobs;

use Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\SharpImageManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;

class HandleTransformedFileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public string $disk,
        public string $filePath,
        public array $transformFilters,
    ) {}

    public function handle(SharpImageManager $imageManager): void
    {
        $img = $imageManager->read(Storage::disk($this->disk)->get($this->filePath));

        if ($rotate = Arr::get($this->transformFilters, 'rotate.angle')) {
            $img->rotate($rotate);
        }

        if ($cropData = Arr::get($this->transformFilters, 'crop')) {
            $img->crop(
                intval(round($img->width() * $cropData['width'])),
                intval(round($img->height() * $cropData['height'])),
                intval(round($img->width() * $cropData['x'])),
                intval(round($img->height() * $cropData['y'])),
            );
        }

        Storage::disk($this->disk)
            ->put($this->filePath, $img->encode());
    }
}
