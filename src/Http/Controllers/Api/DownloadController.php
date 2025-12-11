<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;

class DownloadController extends ApiController
{
    public function show(string $filterKey, string $entityKey, ?string $instanceId = null)
    {
        $this->authorizationManager->check('view', $entityKey, $instanceId);

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
