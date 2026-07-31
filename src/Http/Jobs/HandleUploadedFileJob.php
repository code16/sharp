<?php

namespace Code16\Sharp\Http\Jobs;

use Code16\Sharp\Events\FileSaved;
use Code16\Sharp\Exceptions\Form\SharpFormUpdateException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;

class HandleUploadedFileJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;

    public function __construct(
        public string $uploadedFileName,
        public string $disk,
        public string $filePath,
        public bool $shouldOptimizeImage = true,
        public bool $shouldSanitizeSvg = true,
        public ?array $transformFilters = null,
        public ?string $instanceId = null,
    ) {}

    public function handle(): void
    {
        $tmpDisk = sharp()->config()->get('uploads.tmp_disk');
        $tmpFilePath = sprintf(
            '%s/%s',
            sharp()->config()->get('uploads.tmp_dir'),
            $this->uploadedFileName,
        );

        if ($this->shouldOptimizeImage) {
            OptimizeImageJob::dispatchSync(
                disk: $tmpDisk,
                filePath: $tmpFilePath,
            );
        }

        if ($this->transformFilters) {
            // There are transformation and field was configured to handle transformation on the source image
            HandleTransformedFileJob::dispatchSync(
                disk: $tmpDisk,
                filePath: $tmpFilePath,
                transformFilters: $this->transformFilters,
            );
        }

        if ($this->shouldSanitizeSvg && Storage::disk($tmpDisk)->mimeType($tmpFilePath) === 'image/svg+xml') {
            SanitizeSvgJob::dispatchSync(
                disk: $tmpDisk,
                filePath: $tmpFilePath,
            );
        }

        $path = $this->determineFilePath();

        Storage::disk($this->disk)->put($path, Storage::disk($tmpDisk)->get($tmpFilePath));

        Event::dispatch(new FileSaved(path: $path, disk: $this->disk));
    }

    private function determineFilePath(): string
    {
        if (str_contains($this->filePath, '{id}')) {
            if ($this->instanceId === null) {
                throw new SharpFormUpdateException('Instance ID is required but not provided for file path template containing {id}');
            }

            return str_replace('{id}', $this->instanceId, $this->filePath);
        }

        return $this->filePath;
    }
}
