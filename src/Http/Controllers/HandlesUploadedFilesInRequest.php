<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\EntityList\Commands\Command;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormListField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\SharpForm;
use Code16\Sharp\Http\Controllers\Api\Embeds\HandleEmbed;
use Code16\Sharp\Http\Jobs\CurrentRequestJobs;
use Code16\Sharp\Http\Jobs\HandleTransformedFileJob;
use Code16\Sharp\Http\Jobs\HandleUploadedFileJob;
use Illuminate\Bus\Dispatcher;
use Illuminate\Support\Facades\Bus;

trait HandlesUploadedFilesInRequest
{
    use HandleEmbed;

    protected function handlePostedFiles(
        SharpForm|Command $form,
        $instanceId = null
    ): void {
        app(CurrentRequestJobs::class)->jobs->each->setInstanceId($instanceId);
        app(CurrentRequestJobs::class)
            ->allowFailures()
            ->dispatch();
//        foreach ($form->fieldsContainer()->getFields() as $field) {
//            if ($field instanceof SharpFormUploadField) {
//                $this->handleUploadFieldPostedFile(
//                    uploadField: $field,
//                    filePath: $formattedData[$field->key]['file_name'] ?? null,
//                    fileData: $request[$field->key] ?? null,
//                    instanceId: $instanceId
//                );
//            } elseif ($field instanceof SharpFormListField) {
//                $this->handleListFieldPostedFiles(
//                    listField: $field,
//                    listData: $request[$field->key] ?? null,
//                    formattedListData: $formattedData[$field->key] ?? null,
//                    instanceId: $instanceId
//                );
//            } elseif ($field instanceof SharpFormEditorField) {
//                $this->handleEditorFieldPostedFiles(
//                    editorField: $field,
//                    editorData: $request[$field->key] ?? null,
//                    instanceId: $instanceId
//                );
//            }
//        }
    }

    protected function handleEditorFieldPostedFiles(
        SharpFormEditorField $editorField,
        ?array $editorData,
        $instanceId,
    ): void {
        foreach ($editorData['uploads'] ?? [] as $upload) {
            $this->handleUploadFieldPostedFile(
                uploadField: $editorField->uploadsConfig(),
                filePath: $upload['file']['path'] ?? null,
                fileData: $upload['file'] ?? null,
                instanceId: $instanceId,
            );
        }

        foreach ($editorData['embeds'] ?? [] as $embedKey => $embeds) {
            $embed = $this->getEmbedFromKey($embedKey);

            foreach ($embeds as $embedFields) {
                foreach ($embedFields as $fieldKey => $embedFieldData) {
                    $embedField = $embed->findFieldByKey($fieldKey);

                    if ($embedField instanceof SharpFormListField) {
                        $this->handleListFieldPostedFiles(
                            listField: $embedField,
                            listData: $embedFieldData,
                            formattedListData: $embedFieldData,
                            instanceId: $instanceId
                        );
                    } elseif ($embedField instanceof SharpFormUploadField) {
                        $this->handleUploadFieldPostedFile(
                            uploadField: $embedField,
                            filePath: $embedFieldData['path'] ?? null,
                            fileData: $embedFieldData,
                            instanceId: $instanceId,
                        );
                    }
                }
            }
        }
    }

    protected function handleListFieldPostedFiles(
        SharpFormListField $listField,
        ?array $listData,
        ?array $formattedListData,
        $instanceId,
    ) {
        if ($uploadField = $listField->itemFields()->whereInstanceOf(SharpFormUploadField::class)->first()) {
            foreach ($listData ?? [] as $i => $item) {
                $this->handleUploadFieldPostedFile(
                    uploadField: $uploadField,
                    filePath: $formattedListData[$i][$uploadField->key()]['file_name'] ?? $item[$uploadField->key()]['path'] ?? null,
                    fileData: $item[$uploadField->key()] ?? null,
                    instanceId: $instanceId,
                );
            }
        }
    }

    protected function handleUploadFieldPostedFile(
        SharpFormUploadField $uploadField,
        ?string $filePath,
        ?array $fileData,
        $instanceId
    ): void {
        $wasUploaded = ($fileData['uploaded'] ?? false) && $filePath;
        $wasTransformed = ($fileData['transformed'] ?? false) && $uploadField->isImageTransformOriginal();

        if ($wasUploaded) {
            HandleUploadedFileJob::dispatch(
                uploadedFileName: $fileData['name'],
                disk: $uploadField->storageDisk(),
                filePath: $filePath,
                instanceId: $instanceId,
                shouldOptimizeImage: $uploadField->isImageOptimize(),
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
