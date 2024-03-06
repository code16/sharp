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
            'fields.file' => ['required', 'array'],
            'fields.file.storageBasePath' => ['required', 'string'],
            'fields.file.storageDisk' => ['required', 'string'],
            'data' => ['required', 'array'],
        ]);

        $uploadField = SharpFormEditorUpload::make('file')
            ->setStorageBasePath(request()->input('fields.file.storageBasePath'))
            ->setStorageDisk(request()->input('fields.file.storageDisk'))
            ->setHasLegend(request()->has('fields.legend'));

        $uploadField->formatter()->setAlwaysReturnFullObject();

        $form = new FormEditorUploadForm($uploadField);

        $formattedData = $form->formatAndValidateRequestData(request()->input('data'), $instanceId);

        $transformedData = $form
            ->setCustomTransformer(
                'file',
                (new SharpUploadModelFormAttributeTransformer(withThumbnails: false))->dynamicInstance(),
            )
            ->transform($formattedData);

        return response()->json($transformedData);
    }
}
