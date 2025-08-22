<?php

namespace Code16\Sharp\Http\Jobs;

use enshrined\svgSanitize\Sanitizer;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Storage;

class SanitizeSvgJob implements ShouldQueue
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
        $sanitizer = new Sanitizer();
        $sanitizer->minify(true);
        $sanitizer->removeXMLTag(true);
        $sanitizedSvg = $sanitizer->sanitize(
            Storage::disk($this->disk)->get($this->filePath)
        );

        Storage::disk($this->disk)
            ->put($this->filePath, $sanitizedSvg);
    }
}
