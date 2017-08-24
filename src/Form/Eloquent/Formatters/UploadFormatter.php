<?php

namespace Code16\Sharp\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;
use Code16\Sharp\Utils\FileUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\FilesystemManager;
use Intervention\Image\ImageManager;

class UploadFormatter
{
    /**
     * @var FilesystemManager
     */
    private $filesystem;

    /**
     * @var FileUtil
     */
    private $fileUtil;

    /**
     * @var ImageManager
     */
    private $imageManager;

    /**
     * @param FilesystemManager $filesystem
     * @param FileUtil $uploadUtil
     */
    public function __construct(FilesystemManager $filesystem, FileUtil $uploadUtil, ImageManager $imageManager)
    {
        $this->filesystem = $filesystem;
        $this->fileUtil = $uploadUtil;
        $this->imageManager = $imageManager;
    }

    /**
     * @param $value
     * @param SharpFormUploadField $field
     * @param Model $instance
     * @return array
     */
    public function format($value, $field, Model $instance)
    {
        if($this->isUploaded($value)) {

            $this->checkIfModelNeedsToExist($field, $instance);

            $fileContent = $this->filesystem->disk("sharp_uploads")->get(
                $value["name"]
            );

            if($transformed = $this->isTransformed($value, $field)) {
                // Handle transformations on the uploads disk for performance
                $fileContent = $this->handleImageTransformations($fileContent, $value["cropData"]);
            }

            $storedFileName = $this->getStoragePath($value["name"], $field, $instance);

            $this->filesystem->disk($field->storageDisk())->put(
                $storedFileName, $fileContent
            );

            return [
                "file_name" => $storedFileName,
                "size" => $this->filesystem->disk($field->storageDisk())->size($storedFileName),
                "mime_type" => $this->filesystem->disk($field->storageDisk())->mimeType($storedFileName),
                "disk" => $field->storageDisk(),
                "transformed" => $transformed
            ];
        }

        if($this->isTransformed($value, $field)) {
            // Just transform image, without updating value in DB
            $fileContent = $this->filesystem->disk($field->storageDisk())->get(
                $value["name"]
            );

            $this->filesystem->disk($field->storageDisk())->put(
                $value["name"],
                $this->handleImageTransformations($fileContent, $value["cropData"])
            );

            return [
                "transformed" => true
            ];
        }

        // No change made
        return is_null($value) ? null : [];
    }

    /**
     * @param array $value
     * @return bool
     */
    protected function isUploaded($value): bool
    {
        return isset($value["uploaded"]) && $value["uploaded"];
    }

    /**
     * @param array $value
     * @param SharpFormFieldWithUpload $field
     * @return bool
     */
    protected function isTransformed($value, $field): bool
    {
        return isset($value["cropData"]) && $field->cropRatio();
    }

    /**
     * @param string $fileName
     * @param SharpFormFieldWithUpload $field
     * @param Model $instance
     * @return string
     */
    protected function getStoragePath(string $fileName, $field, Model $instance): string
    {
        $basePath = $this->substituteParameters($field->storageBasePath(), $instance);

        $fileName = $this->fileUtil->findAvailableName(
            $fileName, $basePath, $field->storageDisk()
        );

        return "{$basePath}/{$fileName}";
    }

    /**
     * Check if we need the Model to be persisted, and if so and if he's transient,
     * throw a ModelNotFoundException.
     *
     * @param SharpFormFieldWithUpload $field
     * @param Model $instance
     */
    protected function checkIfModelNeedsToExist($field, Model $instance)
    {
        if (strpos($field->storageBasePath(), '{id}') !== false && !$instance->exists) {
            throw new ModelNotFoundException();
        }
    }

    /**
     * Replace {id} or other params in the storageBasePath string with
     * the corresponding instance values.
     *
     * @param string $basePath
     * @param Model $instance
     * @return string
     */
    protected function substituteParameters(string $basePath, Model $instance)
    {
        preg_match_all('/{([^}]+)}/', $basePath, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $basePath = str_replace($match[0], $instance->{$match[1]}, $basePath);
        }

        return $basePath;
    }

    /**
     * @param $fileContent
     * @param array $cropData
     * @return \Intervention\Image\Image
     */
    protected function handleImageTransformations($fileContent, array $cropData)
    {
        $img = $this->imageManager->make($fileContent);

        if($cropData["rotate"]) {
            $img->rotate($cropData["rotate"]);
        }

        $img->crop(
            intval(round($img->width() * $cropData["width"])),
            intval(round($img->height() * $cropData["height"])),
            intval(round($img->width() * $cropData["x"])),
            intval(round($img->height() * $cropData["y"]))
        );

        return $img->encode();
    }
}