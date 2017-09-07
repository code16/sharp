<?php

namespace Code16\Sharp\Form\Fields\Formatters;

use Code16\Sharp\Form\Fields\SharpFormField;
use Code16\Sharp\Form\Fields\Utils\SharpFormFieldWithUpload;
use Code16\Sharp\Utils\FileUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Filesystem\FilesystemManager;
use Intervention\Image\ImageManager;

class UploadFormatter implements SharpFieldFormatter
{
    /**
     * @var FilesystemManager
     */
    protected $filesystem;

    /**
     * @var FileUtil
     */
    protected $fileUtil;

    /**
     * @var ImageManager
     */
    protected $imageManager;


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
     */
    function fromFront(SharpFormField $field, string $attribute, $value)
    {
        if($this->isUploaded($value)) {

            $fileContent = $this->filesystem->disk("local")->get(
                config("sharp.uploads.tmp_dir", 'tmp') . '/' . $value["name"]
            );

            if($transformed = $this->isTransformed($value, $field)) {
                // Handle transformations on the uploads disk for performance
                $fileContent = $this->handleImageTransformations($fileContent, $value["cropData"]);
            }

            $storedFileName = $this->getStoragePath($value["name"], $field);

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
     * @return string
     */
    protected function getStoragePath(string $fileName, $field): string
    {
        $basePath = $field->storageBasePath();

        $fileName = $this->fileUtil->findAvailableName(
            $fileName, $basePath, $field->storageDisk()
        );

        return "{$basePath}/{$fileName}";
    }

    /**
     * Replace {id} or other params in the storageBasePath string with
     * the corresponding instance values.
     *
     * @param string $basePath
     * @param Model $instance
     * @return string
     */
//    protected function substituteParameters(string $basePath, Model $instance)
//    {
//        preg_match_all('/{([^}]+)}/', $basePath, $matches, PREG_SET_ORDER);
//
//        foreach ($matches as $match) {
//            $basePath = str_replace($match[0], $instance->{$match[1]}, $basePath);
//        }
//
//        return $basePath;
//    }

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