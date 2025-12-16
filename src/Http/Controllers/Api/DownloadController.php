<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Illuminate\Support\Facades\Storage;

class DownloadController extends ApiController
{
    public function show(string $filterKey, string $entityKey, ?string $instanceId = null)
    {
        $this->authorizationManager->check('view', $entityKey, $instanceId);

        if (
            ($allowedDisks = sharp()->config()->get('downloads.allowed_disks')) !== null // Legacy config
            && $allowedDisks != '*'
        ) {
            abort_if(! in_array(request()->get('disk'), $allowedDisks), 403);
        }

        abort_if(
            ! ($path = request()->get('path'))
            || ! ($disk = request()->get('disk'))
            || ! Storage::disk($disk)->exists($path),
            404,
            trans('sharp::errors.file_not_found'),
        );

        return response(
            content: Storage::disk($disk)->get($path),
            headers: [
                'Content-Type' => Storage::disk($disk)->mimeType($path),
                'Content-Disposition' => 'attachment',
            ],
        );
    }
}
