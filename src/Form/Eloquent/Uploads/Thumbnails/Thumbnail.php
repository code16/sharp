<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Str;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\ImageManager;

class Thumbnail
{
    /**
     * @var ImageManager
     */
    protected $imageManager;

    /**
     * @var FilesystemManager
     */
    protected $storage;

    /**
     * @var SharpUploadModel
     */
    protected $uploadModel;

    /**
     * @var int
     */
    protected $quality = 90;

    /**
     * @var bool
     */
    protected $appendTimestamp = false;

    /**
     * @param SharpUploadModel $model
     * @param ImageManager $imageManager
     * @param FilesystemManager $storage
     */
    public function __construct(SharpUploadModel $model, ImageManager $imageManager = null, FilesystemManager $storage = null)
    {
        $this->uploadModel = $model;
        $this->imageManager = $imageManager ?: app(ImageManager::class);
        $this->storage = $storage ?: app(FilesystemManager::class);
    }

    /**
     * @param int $quality
     * @return $this
     */
    public function setQuality(int $quality)
    {
        $this->quality = $quality;

        return $this;
    }

    /**
     * @param bool $appendTimestamp
     * @return $this
     */
    public function setAppendTimestamp(bool $appendTimestamp = true)
    {
        $this->appendTimestamp = $appendTimestamp;

        return $this;
    }

    /**
     * @param int $width
     * @param int|null $height
     * @param array $filters: fit, grayscale, ...
     * @return null|string
     */
    public function make($width, $height=null, $filters=[])
    {
        return $this->generateThumbnail(
            $this->uploadModel->disk,
            $this->uploadModel->file_name,
            dirname($this->uploadModel->file_name),
            basename($this->uploadModel->file_name),
            $width, $height, $filters
        );
    }

    public function destroyAllThumbnails()
    {
        $thumbnailDisk = $this->storage->disk(config("sharp.uploads.thumbnails_disk", "public"));
        $thumbnailPath = config("sharp.uploads.thumbnails_dir", "thumbnails");
        $destinationRelativeBasePath = dirname($this->uploadModel->file_name);

        $thumbnailDisk->deleteDirectory("$thumbnailPath/$destinationRelativeBasePath");
    }

    /**
     * @param $sourceDisk
     * @param $sourceRelativeFilePath
     * @param $destinationRelativeBasePath
     * @param $destinationFileName
     * @param $width
     * @param $height
     * @param $filters
     * @return null|string
     */
    private function generateThumbnail(
        $sourceDisk, $sourceRelativeFilePath,
        $destinationRelativeBasePath, $destinationFileName,
        $width, $height, $filters)
    {
        if($width==0) $width=null;
        if($height==0) $height=null;

        $thumbnailDisk = $this->storage->disk(config("sharp.uploads.thumbnails_disk", "public"));
        $thumbnailPath = config("sharp.uploads.thumbnails_dir", "thumbnails");

        $thumbDirNameAppender = sizeof($filters) ? "_" . md5(serialize($filters)) : "";

        $thumbName = "$thumbnailPath/$destinationRelativeBasePath/$width-$height"
            . $thumbDirNameAppender . "/$destinationFileName";

        if (!$thumbnailDisk->exists($thumbName)) {

            // Create thumbnail directories if needed
            if (!$thumbnailDisk->exists(dirname($thumbName))) {
                $thumbnailDisk->makeDirectory(dirname($thumbName));
            }

            try {
                $sourceImg = $this->imageManager->make(
                    $this->storage->disk($sourceDisk)->get($sourceRelativeFilePath)
                );

                // Filters
                $alreadyResized = false;
                foreach ($filters as $filter => $params) {
                    $filterInstance = $this->resolveFilterClass($filter, $params);
                    if ($filterInstance) {
                        $sourceImg->filter($filterInstance);
                        $alreadyResized = $alreadyResized || $filterInstance->resized();
                    }
                }

                // Resize if needed
                if (!$alreadyResized) {
                    $sourceImg->resize($width, $height, function ($constraint) {
                        $constraint->aspectRatio();
                        $constraint->upsize();
                    });
                }

                $sourceImg->save($thumbnailDisk->path($thumbName), $this->quality);

            } catch(FileNotFoundException $ex) {
                return null;

            } catch(NotReadableException $ex) {
                return null;
            }
        }

        return $thumbnailDisk->url($thumbName) . ($this->appendTimestamp ? "?" . filectime($thumbnailDisk->path($thumbName)) : "");
    }

    /**
     * @param string $class
     * @param array $params
     * @return ThumbnailFilter|null
     */
    private function resolveFilterClass($class, array $params)
    {
        if(! Str::contains($class, "\\")) {
            $class = 'Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\\' . ucfirst($class) . 'Filter';
        }

        if(class_exists($class)) {
            return new $class($params);
        }

        return null;
    }
}