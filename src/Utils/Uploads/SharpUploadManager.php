<?php

namespace Code16\Sharp\Utils\Uploads;

use Code16\Sharp\Http\Jobs\HandleTransformedFileJob;
use Code16\Sharp\Http\Jobs\HandleUploadedFileJob;

class SharpUploadManager
{
    protected array $uploadedFileQueue = [];
    protected array $transformedFileQueue = [];

    public function dispatchJobs(?string $instanceId = null): void
    {
        foreach ($this->uploadedFileQueue as $params) {
            $params['instanceId'] = $instanceId;
            HandleUploadedFileJob::dispatch(...$params)
                ->onQueue(config('sharp.uploads.file_handling_queue', 'default'))
                ->onConnection(config('sharp.uploads.file_handling_queue_connection', 'sync'));
        }
        foreach ($this->transformedFileQueue as $params) {
            HandleTransformedFileJob::dispatch(...$params)
                ->onQueue(config('sharp.uploads.file_handling_queue', 'default'))
                ->onConnection(config('sharp.uploads.file_handling_queue_connection', 'sync'));
        }
    }

    public function queueHandleUploadedFile(
        string $uploadedFileName,
        string $disk,
        string $filePath,
        bool $shouldOptimizeImage = true,
        ?array $transformFilters = null,
    ): void {
        $this->uploadedFileQueue[] = compact(
            'uploadedFileName',
            'disk',
            'filePath',
            'shouldOptimizeImage',
            'transformFilters',
        );
    }

    public function queueHandleTransformedFile(
        string $disk,
        string $filePath,
        array $transformFilters,
    ): void {
        $this->transformedFileQueue[] = compact(
            'disk',
            'filePath',
            'transformFilters',
        );
    }
}
