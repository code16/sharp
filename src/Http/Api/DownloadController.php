<?php

namespace Code16\Sharp\Http\Api;

use Illuminate\Support\Facades\Storage;

class DownloadController extends ApiController
{
    public function show(string $entityKey, ?string $instanceId = null)
    {
        sharp_check_ability('view', $entityKey, $instanceId);

        abort_if(
            ! ($path = request()->get('path'))
            || ! ($disk = request()->get('disk'))
            || ! Storage::disk($disk)->exists($path),
            404,
            trans('sharp::errors.file_not_found'),
        );

        return response(
            Storage::disk($disk)->get($path), 200, [
                'Content-Type' => Storage::disk($disk)->mimeType($path),
                'Content-Disposition' => 'attachment',
            ],
        );
    }
}
