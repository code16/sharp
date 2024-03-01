<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Closure;

interface IsUploadField
{
    public function setMaxFileSize(float $maxFileSizeInMB): self;

    public function setImageCropRatio(string $ratio = null, ?array $transformableFileTypes = null): self;

    public function setImageOptimize(bool $mageOptimize = true): self;

    public function isImageOptimize(): bool;

    public function setImageCompactThumbnail(bool $compactThumbnail = true): self;

    public function setImageTransformable(bool $transformable = true, ?bool $transformKeepOriginal = null): self;

    public function isImageTransformOriginal(): bool;

    public function setStorageDisk(string $storageDisk): self;

    public function setStorageBasePath(string|Closure $storageBasePath): self;

    public function setFileFilter(string|array $fileFilter): self;

    public function setFileFilterImages(): self;

    public function storageDisk(): string;

    public function storageBasePath(): string;

    public function cropRatio(): ?array;
}
