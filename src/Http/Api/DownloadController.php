<?php

namespace Code16\Sharp\Http\Api;

use Code16\Sharp\Http\SharpContext;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Str;

class DownloadController extends ApiController
{
    /** @var FilesystemManager */
    protected $filesystem;

    /**
     * @param FilesystemManager $filesystem
     */
    public function __construct(FilesystemManager $filesystem)
    {
        parent::__construct();
        
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $fieldKey
     * @param string $entityKey
     * @param string|null $instanceId
     * @return \Illuminate\Http\Response
     * @throws \Code16\Sharp\Exceptions\Auth\SharpAuthorizationException
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    public function show($fieldKey, $entityKey, $instanceId = null)
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

    /**
     * @param string $fileName
     * @param string $fieldKey
     * @param string $entityKey
     * @param null $instanceId
     * @return array
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    protected function determineDiskAndFilePath(string $fileName, string $fieldKey, string $entityKey, $instanceId = null)
    {
        $field = $this->getField($entityKey, $fieldKey);
        
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

    /**
     * @param string $entityKey
     * @param string $fieldKey
     * @return \Code16\Sharp\Form\Fields\SharpFormField|\Code16\Sharp\Show\Fields\SharpShowField
     * @throws \Code16\Sharp\Exceptions\SharpInvalidEntityKeyException
     */
    protected function getField(string $entityKey, string $fieldKey)
    {
        $fieldContainer = app(SharpContext::class)->isForm()
            ? $this->getFormInstance($entityKey)
            : $this->getShowInstance($entityKey);
        
        return $fieldContainer->findFieldByKey($fieldKey);
    }
}