<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\FilesystemManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Intervention\Image\Exception\NotReadableException;
use Intervention\Image\ImageManager;
use \Closure;

class Thumbnail
{
    protected ImageManager $imageManager;
    protected FilesystemManager $storage;
    protected SharpUploadModel $uploadModel;
    protected int $quality = 90;
    protected bool $appendTimestamp = false;
    protected ?Closure $afterClosure = null;
    protected ?array $transformationFilters = null;

    public function __construct(SharpUploadModel $model, ImageManager $imageManager = null, FilesystemManager $storage = null)
    {
        $this->uploadModel = $model;
        $this->imageManager = $imageManager ?: app(ImageManager::class);
        $this->storage = $storage ?: app(FilesystemManager::class);
    }

    public function setQuality(int $quality): self
    {
        $this->quality = $quality;

        return $this;
    }

    public function setAfterClosure(Closure $closure): self
    {
        $this->afterClosure = $closure;

        return $this;
    }

    public function setAppendTimestamp(bool $appendTimestamp = true): self
    {
        $this->appendTimestamp = $appendTimestamp;

        return $this;
    }

    public function setTransformationFilters(array $transformationFilters = null): self
    {
        $this->transformationFilters = $transformationFilters;

        return $this;
    }

    public function make(?int $width, ?int $height=null, array $filters=[]): ?string
    {
        $thumbnailDisk = $this->storage->disk(config("sharp.uploads.thumbnails_disk", "public"));

        $thumbDirNameAppender = 
            ($this->transformationFilters ? "_" . md5(serialize($this->transformationFilters)) : "")
            . (sizeof($filters) ? "_" . md5(serialize($filters)) : "");
        
        $thumbnailPath = sprintf(
            "%s/%s/%s-%s%s/%s",
            config("sharp.uploads.thumbnails_dir", "thumbnails"),
            dirname($this->uploadModel->file_name),
            $width, $height, $thumbDirNameAppender,
            basename($this->uploadModel->file_name)
        );

        $wasCreated = !$thumbnailDisk->exists($thumbnailPath);

        $url = $this->generateThumbnail(
            $this->uploadModel->disk,
            $this->uploadModel->file_name,
            $thumbnailPath,
            $width, $height, $filters
        );

        if($closure = $this->afterClosure) {
            $closure($wasCreated, $thumbnailPath, $thumbnailDisk);
        }

        return $url;
    }

    public function destroyAllThumbnails(): void
    {
        $thumbnailDisk = $this->storage->disk(config("sharp.uploads.thumbnails_disk", "public"));
        $thumbnailPath = config("sharp.uploads.thumbnails_dir", "thumbnails");
        $destinationRelativeBasePath = dirname($this->uploadModel->file_name);

        $thumbnailDisk->deleteDirectory("$thumbnailPath/$destinationRelativeBasePath");
    }

    private function generateThumbnail(
        string $sourceDisk, string $sourceRelativeFilePath,
        string $thumbnailPath, ?int $width, ?int $height, array $filters): ?string
    {
        if($width==0) $width=null;
        if($height==0) $height=null;

        $thumbnailDisk = $this->storage->disk(config("sharp.uploads.thumbnails_disk", "public"));

        if (!$thumbnailDisk->exists($thumbnailPath)) {
            // Create thumbnail directories if needed
            if (!$thumbnailDisk->exists(dirname($thumbnailPath))) {
                $thumbnailDisk->makeDirectory(dirname($thumbnailPath));
            }

            try {
                $sourceImg = $this->imageManager->make(
                    $this->storage->disk($sourceDisk)->get($sourceRelativeFilePath)
                );
                
                // Transformation filters
                if($this->transformationFilters) {
                    if($rotate = Arr::get($this->transformationFilters, "rotate.angle")) {
                        $sourceImg->rotate($rotate);
                    }

                    if($cropData = Arr::get($this->transformationFilters, "crop")) {
                        $sourceImg->crop(
                            intval(round($sourceImg->width() * $cropData["width"])),
                            intval(round($sourceImg->height() * $cropData["height"])),
                            intval(round($sourceImg->width() * $cropData["x"])),
                            intval(round($sourceImg->height() * $cropData["y"]))
                        );
                    }
                }

                // Custom filters
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

                $thumbnailDisk->put($thumbnailPath, $sourceImg->stream(null, $this->quality));

            } catch(FileNotFoundException $ex) {
                return null;

            } catch(NotReadableException $ex) {
                return null;
            }
        }

        return $thumbnailDisk->url($thumbnailPath) . ($this->appendTimestamp ? "?" . $thumbnailDisk->lastModified($thumbnailPath) : "");
    }

    private function resolveFilterClass(string $class, array $params): ?ThumbnailFilter
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