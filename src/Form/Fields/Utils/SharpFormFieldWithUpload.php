<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Closure;

trait SharpFormFieldWithUpload
{
    protected ?float $maxFileSize = null;
    protected ?array $cropRatio = null;
    protected ?array $transformableFileTypes = null;
    protected string $storageDisk = "local";
    /** @var string|Closure  */
    protected $storageBasePath = "data";
    protected bool $transformable = true;
    protected bool $transformOriginal = false;
    protected bool $compactThumbnail = false;
    protected bool $shouldOptimizeImage = false;
    /** @var string|array|null */
    protected $fileFilter = null;

    public function setMaxFileSize(float $maxFileSizeInMB): self
    {
        $this->maxFileSize = $maxFileSizeInMB;

        return $this;
    }

    /**
     * @param string $ratio 16:9, 1:1, ...
     * @param array|null $transformableFileTypes
     * @return static
     */
    public function setCropRatio(string $ratio = null, array $transformableFileTypes = null): self
    {
        if($ratio) {
            $this->cropRatio = explode(":", $ratio);

            $this->transformableFileTypes = $transformableFileTypes
                ? $this->formatFileExtension($transformableFileTypes)
                : null;

        } else {
            $this->cropRatio = null;
            $this->transformableFileTypes = null;
        }

        return $this;
    }

    public function shouldOptimizeImage(bool $shouldOptimizeImage = true): self
    {
        $this->shouldOptimizeImage = $shouldOptimizeImage;

        return $this;
    }

    public function isShouldOptimizeImage(): bool
    {
        return $this->shouldOptimizeImage;
    }

    public function setCompactThumbnail(bool $compactThumbnail = true): self
    {
        $this->compactThumbnail = $compactThumbnail;

        return $this;
    }

    /** @deprecated use setTransformable() */
    public function setCroppable(bool $croppable = true): self
    {
        return $this->setTransformable($croppable);
    }

    public function setTransformable(bool $transformable = true, bool $transformOriginal = false): self
    {
        $this->transformable = $transformable;
        if($transformable) {
            $this->transformOriginal = $transformOriginal;
        }

        return $this;
    }

    public function setStorageDisk(string $storageDisk): self
    {
        $this->storageDisk = $storageDisk;

        return $this;
    }

    /**
     * @param string|Closure $storageBasePath
     * @return static
     */
    public function setStorageBasePath($storageBasePath): self
    {
        $this->storageBasePath = $storageBasePath;

        return $this;
    }

    /**
     * @param string|array $fileFilter
     * @return static
     */
    public function setFileFilter($fileFilter): self
    {
        $this->fileFilter = $this->formatFileExtension($fileFilter);

        return $this;
    }

    public function setFileFilterImages(): self
    {
        $this->setFileFilter([".jpg",".jpeg",".gif",".png"]);

        return $this;
    }

    public function storageDisk(): string
    {
        return $this->storageDisk;
    }

    public function storageBasePath(): string
    {
        return value($this->storageBasePath);
    }

    public function cropRatio(): array
    {
        return $this->cropRatio;
    }

    /**
     * @param string|array $fileFilter
     * @return array
     */
    private function formatFileExtension($fileFilter): array
    {
        if (!is_array($fileFilter)) {
            $fileFilter = explode(",", $fileFilter);
        }

        return collect($fileFilter)
            ->map(function ($filter) {
                $filter = trim($filter);
                if (substr($filter, 0, 1) != ".") {
                    $filter = ".$filter";
                }
                return $filter;
            })
            ->all();
    }
}
