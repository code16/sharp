<?php

namespace Code16\Sharp\Form\Fields\Utils;

use Closure;

interface IsUploadField
{
    public function setMaxFileSize(float $maxFileSizeInMB): self;

    public function setCropRatio(string $ratio = null, ?array $transformableFileTypes = null): self;

    public function shouldOptimizeImage(bool $shouldOptimizeImage = true): self;

    public function isShouldOptimizeImage(): bool;

    public function setCompactThumbnail(bool $compactThumbnail = true): self;

    public function setTransformable(bool $transformable = true, ?bool $transformKeepOriginal = null): self;

    public function isTransformOriginal(): bool;

    public function setStorageDisk(string $storageDisk): self;

    public function setStorageBasePath(string|Closure $storageBasePath): self;

    public function setFileFilter(string|array $fileFilter): self;

    public function setFileFilterImages(): self;

    public function storageDisk(): string;

    public function storageBasePath(): string;

    public function cropRatio(): array;
}
