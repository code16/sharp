<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Http\Controllers\Api\Requests\EditorUploadFormRequest;

class ApiFormEditorUploadFormController extends ApiController
{
    public function update(string $globalFilter, EditorUploadFormRequest $request, string $entityKey, ?string $instanceId = null)
    {
        return response()->json($request->input('data'));
    }
}
