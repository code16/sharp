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
                        fileData: $request[$field->key] ?? null,
                        disk: $field->storageDisk(),
                        filePath: $formattedData[$field->key]['file_name'] ?? null,
                        transformOriginal: $field->isTransformOriginal(),
                        shouldOptimizeImage: $field->isShouldOptimizeImage(),
                        instanceId: $instanceId
                    );
                } elseif ($field instanceof SharpFormEditorField) {
                    collect($request[$field->key]['files'] ?? [])
                        ->each(function (array $file) use ($field, $instanceId) {
                            $this->handleFieldPostedFile(
                                fileData: $file,
                                disk: $file['disk'] ?? 'local',
                                filePath: $file['path'] ?? null,
                                transformOriginal: $file['transformOriginal'] ?? false,
                                shouldOptimizeImage: $file['shouldOptimizeImage'] ?? false,
                                instanceId: $instanceId,
                            );
                        });
                }
            });
    }

    protected function handleFieldPostedFile(
        array $fileData,
        ?string $disk,
        ?string $filePath,
        bool $transformOriginal,
        bool $shouldOptimizeImage,
        $instanceId
    ) {
        $wasUploaded = ($fileData['uploaded'] ?? false) && $filePath;
        $wasTransformed = ($fileData['transformed'] ?? false) && $transformOriginal;

        if ($wasUploaded) {
            HandleUploadedFileJob::dispatch(
                uploadedFileName: $fileData['name'],
                disk: $disk,
                filePath: $filePath,
                instanceId: $instanceId,
                shouldOptimizeImage: $shouldOptimizeImage,
                transformFilters: $wasTransformed
                    ? $fileData['filters']
                    : null,
            );
        } elseif ($wasTransformed) {
            HandleTransformedFileJob::dispatch(
                disk: $disk,
                filePath: $filePath,
                transformFilters: $fileData['filters'],
            );
        }
    }
}
