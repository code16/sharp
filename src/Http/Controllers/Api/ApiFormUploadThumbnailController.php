<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Form\Eloquent\Uploads\Traits\UsesSharpUploadModel;
use Illuminate\Routing\Controller;

class ApiFormUploadThumbnailController extends Controller
{
    use UsesSharpUploadModel;

    // Used to generate large thumbnail for upload crop modal
    public function show(string $entityKey, ?string $instanceId = null)
    {
        sharp_check_ability('view', $entityKey, $instanceId);

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
