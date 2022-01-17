<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Exceptions\Form\SharpFormFieldFormattingMustBeDelayedException;
use Code16\Sharp\Form\Fields\SharpFormEditorField;
use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;
use Code16\Sharp\Utils\FileUtil;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Arr;
use Intervention\Image\Image;
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
     *
     * @return mixed
     */
    public function toFront(SharpFormField $field, $value)
    {
        return $value;
    }

    /**
     * @param SharpFormFieldWithUpload $field
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws SharpFormFieldFormattingMustBeDelayedException
     */
    public function fromFront(SharpFormField $field, string $attribute, $value): ?array
    {
        $storage = $this->filesystem->disk($field->storageDisk());
        $transformed = $value['transformed'] ?? false;

        if ($value['uploaded'] ?? false) {
            $uploadedFieldRelativePath = sprintf(
                '%s/%s',
                config('sharp.uploads.tmp_dir', 'tmp'),
                $value['name']
            );

            if ($field->isShouldOptimizeImage()) {
                $optimizerChain = OptimizerChainFactory::create();
                // We do not need to check for exception nor file format because
                // the package will not throw any errors and just operate silently.
                $optimizerChain->optimize(
                    $this->filesystem->disk('local')->path($uploadedFieldRelativePath)
                );
            }

            $storedFilePath = $this->getStoragePath($value['name'], $field);
            $fileContent = $this->filesystem->disk('local')->get($uploadedFieldRelativePath);

            if ($transformed && $field->isTransformOriginal()) {
                // Field was configured to handle transformation on the source image
                $fileContent = $this->handleImageTransformations($fileContent, $value);
            }

            $storage->put($storedFilePath, $fileContent);

            return [
                'file_name' => $storedFilePath,
                'size'      => $storage->size($storedFilePath),
                'mime_type' => $storage->mimeType($storedFilePath),
                'disk'      => $field->storageDisk(),
                'filters'   => $value['filters'] ?? null,
            ];
        }

        if ($transformed) {
            // Existing image, but transformed (with filters)
            if ($field->isTransformOriginal()) {
                // Field was configured to handle transformation on the source image
                $storage->put(
                    $value['name'],
                    $this->handleImageTransformations($storage->get($value['name']), $value)
                );
            }

            return [
                'filters' => $value['filters'] ?? null,
            ];
        }

        // No change made
        return $value === null ? null : [];
    }

    /**
     * @throws SharpFormFieldFormattingMustBeDelayedException
     */
    protected function getStoragePath(string $fileName, SharpFormUploadField|SharpFormEditorField $field): string
    {
        $basePath = $field->storageBasePath();

        if (str_contains($basePath, '{id}')) {
            if (!$this->instanceId) {
                // Well, we need the instance id for the storage path, and we are
                // in a store() case. Let's delay this formatter, it will be
                // called again after a first save() on the model.
                throw new SharpFormFieldFormattingMustBeDelayedException();
            }

            $basePath = str_replace('{id}', $this->instanceId, $basePath);
        }

        $fileName = $this->fileUtil->findAvailableName(
            $fileName,
            $basePath,
            $field->storageDisk()
        );

        return "{$basePath}/{$fileName}";
    }

    protected function handleImageTransformations($fileContent, array &$filters): Image
    {
        $img = $this->imageManager->make($fileContent);

        if ($rotate = Arr::get($filters, 'filters.rotate.angle')) {
            $img->rotate($rotate);
            unset($filters['filters']['rotate']);
        }

        if ($cropData = Arr::get($filters, 'filters.crop')) {
            $img->crop(
                intval(round($img->width() * $cropData['width'])),
                intval(round($img->height() * $cropData['height'])),
                intval(round($img->width() * $cropData['x'])),
                intval(round($img->height() * $cropData['y']))
            );
            unset($filters['filters']['crop']);
        }

        return $img->encode();
    }
}
