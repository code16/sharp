<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Auth\SharpAuthorizationManager;
use Code16\Sharp\Form\Eloquent\Uploads\Traits\UsesSharpUploadModel;
use Illuminate\Routing\Controller;

class ApiFormUploadThumbnailController extends Controller
{
    use UsesSharpUploadModel;

    public function __construct(private readonly SharpAuthorizationManager $authorizationManager) {}

    // Used to generate large thumbnail for upload crop modal
    public function show(string $filterKey, string $entityKey, ?string $instanceId = null)
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
