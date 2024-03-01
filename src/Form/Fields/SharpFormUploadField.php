<?php

namespace Code16\Sharp\Form\Fields;

use Closure;
use Code16\Sharp\Form\Fields\Formatters\UploadFormatter;
use Code16\Sharp\Form\Fields\Utils\IsUploadField;
use Code16\Sharp\Utils\Fields\Validation\SharpFileValidation;
use Code16\Sharp\Utils\Fields\Validation\SharpImageValidation;
use Illuminate\Validation\Rules\Dimensions;

class SharpFormUploadField extends SharpFormField implements IsUploadField
{
    const FIELD_TYPE = 'upload';

    protected ?array $cropRatio = null;
    protected ?array $transformableFileTypes = null;
    protected string $storageDisk = 'local';
    protected string|Closure $storageBasePath = 'data';
    protected bool $transformable = true;
    protected ?bool $transformKeepOriginal = null;
    protected bool $isImageOnly = false;
    protected ?Dimensions $imageDimensions = null;
    protected bool $compactThumbnail = false;
    protected bool $shouldOptimizeImage = false;
    protected ?float $maxFileSize = null;
    protected ?float $minFileSize = null;
    protected array $fileFilter = [];

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, app(UploadFormatter::class));
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

    public function setMaxFileSize(float $maxFileSizeInMB): self
    {
        $this->maxFileSize = $maxFileSizeInMB;

        return $this;
    }

    public function setMinFileSize(float $minFileSizeInMB): self
    {
        $this->minFileSize = $minFileSizeInMB;

        return $this;
    }

    public function setImageOnly(bool $imageOnly = true): self
    {
        $this->isImageOnly = $imageOnly;

        if (! $this->fileFilter) {
            $this->setFileFilterImages();
        }

        return $this;
    }

    public function setImageDimensions(Dimensions $dimensions): self
    {
        $this->imageDimensions = $dimensions;

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

    public function isTransformable(): bool
    {
        return $this->transformable;
    }

    public function isTransformOriginal(): bool
    {
        return $this->transformable && ! $this->isTransformKeepOriginal();
    }

    public function isTransformKeepOriginal(): bool
    {
        return $this->transformKeepOriginal ?? config('sharp.uploads.transform_keep_original_image', true);
    }

    public function transformableFileTypes(): ?array
    {
        return $this->transformableFileTypes;
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
        /** @see \Illuminate\Validation\Concerns\ValidatesAttributes::validateImage() */
        $this->setFileFilter(['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg', '.webp']);

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

    public function cropRatio(): ?array
    {
        return $this->cropRatio;
    }

    private function formatFileExtension(string|array $fileFilter): array
    {
        if (! is_array($fileFilter)) {
            $fileFilter = explode(',', $fileFilter);
        }

        return collect($fileFilter)
            ->map(fn ($filter) => str($filter)->trim()->start('.')->value())
            ->all();
    }

    protected function validationRules(): array
    {
        return [
            'rule' => 'array',
            'ratioX' => 'integer|nullable',
            'ratioY' => 'integer|nullable',
            'transformable' => 'boolean',
            'transformableFileTypes' => 'array',
            'transformKeepOriginal' => 'boolean',
            'compactThumbnail' => 'boolean',
        ];
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'validation' => $this->buildValidation(),
            'ratioX' => $this->cropRatio ? (int) $this->cropRatio[0] : null,
            'ratioY' => $this->cropRatio ? (int) $this->cropRatio[1] : null,
            'transformable' => $this->transformable,
            'transformableFileTypes' => $this->transformableFileTypes,
            'transformKeepOriginal' => $this->isTransformKeepOriginal(),
            'compactThumbnail' => (bool) $this->compactThumbnail,
            'storageBasePath' => $this->storageBasePath,
            'storageDisk' => $this->storageDisk,
            'shouldOptimizeImage' => $this->shouldOptimizeImage,
        ]);
    }

    private function buildValidation(): array
    {
        $validationRule = $this->isImageOnly
            ? SharpImageValidation::make()
            : SharpFileValidation::make();

        $maxFileSizeInMB = $this->maxFileSize ?: config('sharp.uploads.max_file_size');

        $validationRule
            ->max($maxFileSizeInMB * 1024)
            ->when($this->fileFilter, fn (SharpFileValidation $file) => $file->extensions($this->fileFilter))
            ->when($this->minFileSize, fn (SharpFileValidation $file) => $file->min($this->minFileSize * 1024))
            ->when(
                $validationRule instanceof SharpImageValidation && $this->imageDimensions,
                fn (SharpImageValidation $file) => $file->dimensions($this->imageDimensions)
            );

        return [
            'rule' => $validationRule->toArray(),
            'allowedExtensions' => $this->fileFilter,
            'maximumFileSize' => $maxFileSizeInMB * 1024,
        ];
    }
}
