<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Illuminate\Routing\Controller;

class ApiFormEditorUploadFormController extends Controller
{
    public function update(string $entityKey, ?string $instanceId = null)
    {
        request()->validate([
            'data.file' => ['required'],
        ]);

        return response()->json(request()->input('data'));
    }
}
