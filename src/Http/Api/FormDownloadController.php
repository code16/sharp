<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;
use Illuminate\Filesystem\FilesystemManager;

class FormDownloadController extends ApiController
{
    /**
     * @var FilesystemManager
     */
    protected $filesystem;

    /**
     * @param FilesystemManager $filesystem
     */
    public function __construct(FilesystemManager $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $entityKey
     * @param string $instanceId
     * @param string $formUploadFieldKey
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($entityKey, $instanceId, $formUploadFieldKey)
    {
        sharp_check_ability("view", $entityKey, $instanceId);

        $form = $this->getFormInstance($entityKey);

        list($disk, $path) = $this->determineDiskAndFilePath(
            request('fileName'), $instanceId, $form->findFieldByKey($formUploadFieldKey)
        );

        abort_if(!$disk->exists($path), 404, trans("sharp::errors.file_not_found"));

        return response(
            $disk->get($path), 200, [
                'Content-Type' => $disk->mimeType($path),
                "Content-Disposition" => "attachment"
            ]
        );
    }

    /**
     * @param string $fileName
     * @param string $instanceId
     * @param SharpFormFieldWithUpload $field
     * @return array
     */
    protected function determineDiskAndFilePath(string $fileName, $instanceId, $field)
    {
        $basePath = str_replace('{id}', $instanceId, $field->storageBasePath());
        $disk = $this->filesystem->disk($field->storageDisk());

        if(starts_with($fileName, $basePath)) {
            return [$disk, $fileName];
        }

        return [$disk, "$basePath/$fileName"];
    }

}