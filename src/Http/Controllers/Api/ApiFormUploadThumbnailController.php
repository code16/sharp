<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Form\Eloquent\Uploads\Traits\UsesSharpUploadModel;
use Code16\Sharp\Http\Controllers\Controller;

class ApiFormUploadThumbnailController extends Controller
{
    use UsesSharpUploadModel;

    // Used to generate large thumbnail for upload crop modal
    public function show(string $globalFilter, string $entityKey, ?string $instanceId = null)
    {
        if (! request()->hasValidSignature()) {
            abort(401);
        }

        $this->authorizationManager->check('view', $entityKey, $instanceId);

        // Only the signed query string is trusted: request body must not be able to override it.
        $disk = request()->query('disk');
        $path = request()->query('path');

        if (
            ($allowedDisks = sharp()->config()->get('downloads.allowed_disks')) !== null
            && $allowedDisks != '*'
        ) {
            abort_if(! in_array($disk, $allowedDisks), 403);
        }

        abort_if(! is_string($disk) || ! is_string($path), 404);

        return response()->json([
            'thumbnail' => static::getUploadModelClass()::make([
                'file_name' => $path,
                'disk' => $disk,
            ])
                ->thumbnail(
                    request()->query('width'),
                    request()->query('height')
                ),
        ]);
    }
}
