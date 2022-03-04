<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Closure;

trait SharpFormFieldWithUpload
{
    protected ?float $maxFileSize = null;
    protected ?array $cropRatio = null;
    protected ?array $croppableFileTypes = null;
    protected string $storageDisk = 'local';
    /** @var string|Closure */
    protected $storageBasePath = 'data';
    protected bool $croppable = true;
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
     * @param string     $ratio              16:9, 1:1, ...
     * @param array|null $croppableFileTypes
     *
     * @return static
     */
    public function setCropRatio(string $ratio = null, array $croppableFileTypes = null): self
    {
        if ($ratio) {
            $this->cropRatio = explode(':', $ratio);

            $this->croppableFileTypes = $croppableFileTypes
                ? $this->formatFileExtension($croppableFileTypes)
                : null;
        } else {
            $this->cropRatio = null;
            $this->croppableFileTypes = null;
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

    public function setCroppable(bool $croppable = true): self
    {
        $this->croppable = $croppable;

        return $this;
    }

    public function setStorageDisk(string $storageDisk): self
    {
        $this->storageDisk = $storageDisk;

        return $this;
    }

    /**
     * @param string|Closure $storageBasePath
     *
     * @return static
     */
    public function setStorageBasePath($storageBasePath): self
    {
        $this->storageBasePath = $storageBasePath;

        return $this;
    }

    /**
     * @param string|array $fileFilter
     *
     * @return static
     */
    public function setFileFilter($fileFilter): self
    {
        $this->fileFilter = $this->formatFileExtension($fileFilter);

        return $this;
    }

    public function setFileFilterImages(): self
    {
        $this->setFileFilter(['.jpg', '.jpeg', '.gif', '.png']);

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

    public function cropRatio(): string
    {
        return $this->cropRatio;
    }

    /**
     * @param string|array $fileFilter
     *
     * @return array
     */
    private function formatFileExtension($fileFilter): array
    {
        if (!is_array($fileFilter)) {
            $fileFilter = explode(',', $fileFilter);
        }

        return collect($fileFilter)
            ->map(function ($filter) {
                $filter = trim($filter);
                if (substr($filter, 0, 1) != '.') {
                    $filter = ".$filter";
                }

                return $filter;
            })
            ->all();
    }
}
