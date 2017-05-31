<?php

namespace Code16\Sharp\Form\Eloquent\Formatters;

use Code16\Sharp\Form\Fields\SharpFormUploadField;
use Code16\Sharp\Utils\FileUtil;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Filesystem\FilesystemManager;

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
     * UploadFormatter constructor.
     * @param FilesystemManager $filesystem
     * @param FileUtil $uploadUtil
     */
    public function __construct(FilesystemManager $filesystem, FileUtil $uploadUtil)
    {
        $this->filesystem = $filesystem;
        $this->fileUtil = $uploadUtil;
    }

    /**
     * @param $value
     * @param SharpFormUploadField $field
     * @param Model $instance
     * @return mixed
     */
    public function format($value, SharpFormUploadField $field, Model $instance)
    {
        if($this->isUploaded($value)) {

            $this->checkIfModelNeedsToExist($field, $instance);

            $fileContent = $this->filesystem->disk("sharp_uploads")->get(
                $value["name"]
            );

            $storedFileName = $this->getStoragePath($value["name"], $field, $instance);

            $this->filesystem->disk($field->storageDisk())->put(
                $storedFileName, $fileContent
            );

            return $storedFileName;
        }
    }

    /**
     * @param $value
     * @return bool
     */
    protected function isUploaded($value): bool
    {
        return isset($value["uploaded"]) && $value["uploaded"];
    }

    /**
     * @param string $fileName
     * @param SharpFormUploadField $field
     * @param Model $instance
     * @return string
     */
    protected function getStoragePath(string $fileName, SharpFormUploadField $field, Model $instance): string
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
     * @param SharpFormUploadField $field
     * @param Model $instance
     */
    protected function checkIfModelNeedsToExist(SharpFormUploadField $field, Model $instance)
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
}