<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Form\Eloquent\Uploads\Traits\UsesSharpUploadModel;

class ApiFormUploadThumbnailController extends ApiController
{
    use UsesSharpUploadModel;

    // Used to generate large thumbnail for upload crop modal
    public function show(string $globalFilter, string $entityKey, ?string $instanceId = null)
    {
        $this->authorizationManager->check('view', $entityKey, $instanceId);

        return response()->json([
            'thumbnail' => static::getUploadModelClass()::make([
                'file_name' => request()->input('path'),
                'disk' => request()->input('disk'),
            ])
                ->thumbnail(
                    request()->input('width'),
                    request()->input('height')
                ),
        ]);
    }
}
