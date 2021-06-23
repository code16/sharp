<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;
use Code16\Sharp\Utils\FileUtil;
use Illuminate\Filesystem\FilesystemManager;
use Intervention\Image\ImageManager;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class UploadFormatter extends SharpFieldFormatter
{
    protected FilesystemManager $filesystem;
    protected FileUtil $fileUtil;
    protected ImageManager $imageManager;

    public function __construct()
    {
        $this->filesystem = app(FilesystemManager::class);
        $this->fileUtil = app(FileUtil::class);
        $this->imageManager = app(ImageManager::class);
    }

    /**
     * @param SharpFormField $field
     * @param $value
     * @return mixed
     */
    function toFront(SharpFormField $field, $value)
    {
        return $value;
    }

    /**
     * Handle file operations on newly uploaded files
     * + image transformations (crop, rotations) on transformed ones.
     *
     * @param SharpFormField $field
     * @param string $attribute
     * @param $value
     * @return array|null
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws SharpFormFieldFormattingMustBeDelayedException
     */
    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        $storage = $this->filesystem->disk($field->storageDisk());

        if($value["uploaded"] ?? false) {
            $uploadedFieldRelativePath = sprintf(
                "%s/%s", 
                config("sharp.uploads.tmp_dir", 'tmp'), 
                $value["name"]
            );
            
            if($field->isShouldOptimizeImage()) {
                $optimizerChain = OptimizerChainFactory::create();
                // we do not need to check for exception nor file format because:
                // > By default the package will not throw any errors and just operate silently.
                $optimizerChain->optimize(
                    $this->filesystem->disk("local")->path($uploadedFieldRelativePath)
                );
            }

            $storedFilePath = $this->getStoragePath($value["name"], $field);
            $storage->put(
                $storedFilePath,
                $this->filesystem->disk("local")->get($uploadedFieldRelativePath)
            );

            return [
                "file_name" => $storedFilePath,
                "size" => $storage->size($storedFilePath),
                "mime_type" => $storage->mimeType($storedFilePath),
                "disk" => $field->storageDisk(),
                "filters" => $value["filters"] ?? null
            ];
        }

        if($value["transformed"] ?? false) {
            // Existing image, but transformed (with filters)
            return [
                "filters" => $value["filters"] ?? null
            ];
        }

        // No change made
        return is_null($value) ? null : [];
    }

    /**
     * @param string $fileName
     * @param SharpFormFieldWithUpload $field
     * @return string
     * @throws SharpFormFieldFormattingMustBeDelayedException
     */
    protected function getStoragePath(string $fileName, $field): string
    {
        $basePath = $field->storageBasePath();

        if(strpos($basePath, '{id}') !== false) {
            if(!$this->instanceId) {
                // Well, we need the instance id for the storage path, and we are
                // in a store() case. Let's delay this formatter, it will be
                // called again after a first save() on the model.
                throw new SharpFormFieldFormattingMustBeDelayedException();
            }

            $basePath = str_replace('{id}', $this->instanceId, $basePath);
        }

        $fileName = $this->fileUtil->findAvailableName(
            $fileName, $basePath, $field->storageDisk()
        );

        return "{$basePath}/{$fileName}";
    }
}
