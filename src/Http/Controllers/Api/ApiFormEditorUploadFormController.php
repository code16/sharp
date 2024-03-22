<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\Editor\Uploads\FormEditorUploadForm;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
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
