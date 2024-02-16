<?php

namespace Code16\Sharp\Http\Controllers\Api;

use Code16\Sharp\Data\Form\Fields\FormUploadFieldData;
use Code16\Sharp\Form\Eloquent\Uploads\Transformers\SharpUploadModelFormAttributeTransformer;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUpload;
use Code16\Sharp\Form\Fields\Editor\Uploads\SharpFormEditorUploadForm;
use Illuminate\Routing\Controller;

class ApiFormEditorUploadFormController extends Controller
{
    public function update()
    {
        $uploadFieldData = FormUploadFieldData::from(request()->input('fields.file'));
        
        $form = new SharpFormEditorUploadForm(
            SharpFormEditorUpload::make('file')
                ->setStorageBasePath($uploadFieldData->storageBasePath)
                ->setStorageDisk($uploadFieldData->storageDisk)
                ->setHasLegend(request()->has('fields.legend'))
        );
        
        $formattedData = $form->formatAndValidateRequestData(request()->get('data'));
        $transformedData = $form
            ->setCustomTransformer(
                'file',
                (new SharpUploadModelFormAttributeTransformer(withThumbnails: false))
                    ->with([
                        ...request()->input('data.file.uploaded') ? ['uploaded' => true] : [],
                        ...request()->input('data.file.transformed') ? ['transformed' => true] : [],
                    ])
                    ->dynamicInstance(),
            )
            ->transform($formattedData);

        return response()->json($transformedData);
    }
}
