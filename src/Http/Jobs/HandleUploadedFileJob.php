<?php

namespace Code16\Sharp\Http\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class HandleUploadedFileJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable;

    public function __construct(
        public string $uploadedFileName,
        public string $disk,
        public string $filePath,
        public bool $shouldOptimizeImage = true,
        public ?array $transformFilters = null,
        public ?string $instanceId = null,
    ) {
    }

    public function handle(): void
    {
        $tmpDisk = sharp()->config()->get('uploads.tmp_disk');
        $tmpFilePath = sprintf(
            '%s/%s',
            sharp()->config()->get('uploads.tmp_dir'),
            $this->uploadedFileName,
        );

        if ($this->shouldOptimizeImage) {
            // We do not need to check for exception nor file format because
            // the package will not throw any errors and just operate silently.
            app(OptimizerChainFactory::class)
                ->create()
                ->optimize(Storage::disk($tmpDisk)->path($tmpFilePath));
        }

        if ($this->transformFilters) {
            // There are transformation and field was configured to handle transformation on the source image
            HandleTransformedFileJob::dispatchSync(
                $tmpDisk,
                $tmpFilePath,
                $this->transformFilters
            );
        }

        Storage::disk($this->disk)
            ->put($this->determineFilePath(), Storage::disk($tmpDisk)->get($tmpFilePath));
    }

    private function determineFilePath(): string
    {
        if ($this->instanceId) {
            return str_replace('{id}', $this->instanceId, $this->filePath);
        }

        return $this->filePath;
    }
}
