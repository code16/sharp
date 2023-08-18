<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Closure;

trait SharpFormFieldWithUpload
{
    protected ?float $maxFileSize = null;
    protected ?array $cropRatio = null;
    protected ?array $transformableFileTypes = null;
    protected string $storageDisk = 'local';
    protected string|Closure $storageBasePath = 'data';
    protected bool $transformable = true;
    protected ?bool $transformKeepOriginal = null;
    protected bool $compactThumbnail = false;
    protected bool $shouldOptimizeImage = false;
    protected string|array|null $fileFilter = null;

    public function setMaxFileSize(float $maxFileSizeInMB): self
    {
        $this->maxFileSize = $maxFileSizeInMB;

        return $this;
    }

    public function setCropRatio(string $ratio = null, ?array $transformableFileTypes = null): self
    {
        if ($ratio) {
            $this->cropRatio = explode(':', $ratio);

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

    public function setTransformable(bool $transformable = true, ?bool $transformKeepOriginal = null): self
    {
        $this->transformable = $transformable;

        if ($transformable && ! is_null($transformKeepOriginal)) {
            $this->transformKeepOriginal = $transformKeepOriginal;
        }

        return $this;
    }

    public function isTransformOriginal(): bool
    {
        return $this->transformable && ! $this->transformKeepOriginal();
    }

    protected function transformKeepOriginal(): bool
    {
        return $this->transformKeepOriginal ?? config('sharp.uploads.transform_keep_original_image', true);
    }

    public function setStorageDisk(string $storageDisk): self
    {
        $this->storageDisk = $storageDisk;

        return $this;
    }

    public function setStorageBasePath(string|Closure $storageBasePath): self
    {
        $this->storageBasePath = $storageBasePath;

        return $this;
    }

    public function setFileFilter(string|array $fileFilter): self
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

    public function cropRatio(): array
    {
        return $this->cropRatio;
    }

    private function formatFileExtension(string|array $fileFilter): array
    {
        if (! is_array($fileFilter)) {
            $fileFilter = explode(',', $fileFilter);
        }

        return collect($fileFilter)
            ->map(function ($filter) {
                $filter = trim($filter);
                if (! str_starts_with($filter, '.')) {
                    $filter = ".$filter";
                }

                return $filter;
            })
            ->all();
    }
}
