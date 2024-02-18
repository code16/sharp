<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\EntityList\Commands\Command;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Fields\Utils\IsUploadField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Http\Jobs\HandleTransformedFileJob;
use Code16\Sharp\Http\Jobs\HandleUploadedFileJob;

trait HandlesUploadedFilesInRequest
{
    protected function handlePostedFiles(
        SharpForm|Command $form,
        array $request,
        array $formattedData,
        $instanceId = null
    ): void {
        collect($form->fieldsContainer()->getFields())
            ->each(function (SharpFormField $field) use ($instanceId, $request, $formattedData) {
                if ($field instanceof SharpFormUploadField) {
                    $this->handleFieldPostedFile(
                        uploadField: $field,
                        filePath: $formattedData[$field->key]['file_name'] ?? null,
                        fileData: $request[$field->key] ?? null,
                        instanceId: $instanceId
                    );
                } elseif ($field instanceof SharpFormEditorField) {
                    collect($request[$field->key]['files'] ?? [])
                        ->each(function (array $file) use ($field, $instanceId) {
                            $this->handleFieldPostedFile(
                                uploadField: $field->uploadsConfig(),
                                filePath: $file['path'] ?? null, // <x-sharp-file> case
                                fileData: $file,
                                instanceId: $instanceId,
                            );
                        });
                }
            });
    }

    protected function handleFieldPostedFile(
        IsUploadField $uploadField,
        ?string $filePath,
        array $fileData,
        $instanceId = null
    ) {
        $wasUploaded = ($fileData['uploaded'] ?? false) && $filePath;
        $wasTransformed = $uploadField->isTransformOriginal()
            && ($fileData['transformed'] ?? false);

        if ($wasUploaded) {
            HandleUploadedFileJob::dispatch(
                uploadedFileName: $fileData['name'],
                disk: $uploadField->storageDisk(),
                filePath: $filePath,
                instanceId: $instanceId,
                shouldOptimizeImage: $uploadField->isShouldOptimizeImage(),
                transformFilters: $wasTransformed
                    ? $fileData['filters']
                    : null,
            );
        } elseif ($wasTransformed) {
            HandleTransformedFileJob::dispatch(
                disk: $uploadField->storageDisk(),
                filePath: $filePath,
                transformFilters: $fileData['filters'],
            );
        }
    }
}
