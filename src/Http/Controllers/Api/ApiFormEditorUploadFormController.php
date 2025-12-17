<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Http\Controllers\Api\Requests\EditorUploadFormRequest;
use Illuminate\Routing\Controller;

class ApiFormEditorUploadFormController extends Controller
{
    public function update(string $globalFilter, EditorUploadFormRequest $request, string $entityKey, ?string $instanceId = null)
    {
        return response()->json($request->input('data'));
    }
}
