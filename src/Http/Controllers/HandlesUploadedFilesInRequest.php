<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\EntityList\Commands\Command;
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
    ): void
    {
        collect($form->fieldsContainer()->getFields())
            ->filter(fn ($field) => $field instanceof IsUploadField)
            ->each(function (IsUploadField $field) use ($instanceId, $request, $formattedData) {
                $wasUploaded = ($request[$field->key]['uploaded'] ?? false)
                    && ($formattedData[$field->key]['file_name'] ?? false);
                $wasTransformed = $field->isTransformOriginal()
                    && ($request[$field->key]['transformed'] ?? false);

                if ($wasUploaded) {
                    HandleUploadedFileJob::dispatch(
                        uploadedFileName: $request[$field->key]['name'],
                        disk: $field->storageDisk(),
                        filePath: $formattedData[$field->key]['file_name'],
                        instanceId: $instanceId,
                        shouldOptimizeImage: $field->isShouldOptimizeImage(),
                        transformFilters: $wasTransformed
                            ? $request[$field->key]['filters']
                            : null,
                    );
                } elseif ($wasTransformed) {
                    HandleTransformedFileJob::dispatch(
                        disk: $field->storageDisk(),
                        fileData: $formattedData[$field->key]['file_name'],
                        transformFilters: $request[$field->key]['filters'],
                    );
                }
            });
    }
}
