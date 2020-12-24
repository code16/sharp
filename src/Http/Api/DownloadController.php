<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Exceptions\SharpException;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Str;

class DownloadController extends ApiController
{
    protected FilesystemManager $filesystem;

    public function __construct(FilesystemManager $filesystem)
    {
        parent::__construct();
        
        $this->filesystem = $filesystem;
    }

    public function show(string $fieldKey, string $entityKey, ?string $instanceId = null)
    {
        sharp_check_ability("view", $entityKey, $instanceId);
        
        list($disk, $path) = $this->determineDiskAndFilePath(
            request('fileName'), $fieldKey, $entityKey, $instanceId
        );

        abort_if(!$disk->exists($path), 404, trans("sharp::errors.file_not_found"));

        return response(
            $disk->get($path), 200, [
                'Content-Type' => $disk->mimeType($path),
                "Content-Disposition" => "attachment"
            ]
        );
    }

    protected function determineDiskAndFilePath(string $fileName, string $fieldKey, string $entityKey, string $instanceId = null): array
    {
        if(!$field = $this->getField($entityKey, $fieldKey)) {
            throw new SharpException("Field [$fieldKey] can't be found in Form or Show of [$entityKey]");
        }
        
        $basePath = $instanceId 
            ? str_replace('{id}', $instanceId, $field->storageBasePath())
            : $field->storageBasePath();
        
        $storageDisk = $field->storageDisk();

        if(strpos($fileName, ":") !== false) {
            // Disk name is part of the file name, as in "local:/my/file.jpg".
            // This is the case in markdown embedded images.
            list($storageDisk, $fileName) = explode(":", $fileName);
        }

        $disk = $this->filesystem->disk($storageDisk);

        if(Str::startsWith($fileName, $basePath)) {
            return [$disk, $fileName];
        }

        return [$disk, "$basePath/$fileName"];
    }

    protected function getField(string $entityKey, string $fieldKey)
    {
        $fieldContainer = $this->currentSharpRequest->isForm()
            ? $this->getFormInstance($entityKey)
            : $this->getShowInstance($entityKey);
        
        return $fieldContainer->findFieldByKey($fieldKey);
    }
}