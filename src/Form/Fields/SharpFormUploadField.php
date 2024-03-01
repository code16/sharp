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

    protected string $storageDisk = 'local';
    protected string|Closure $storageBasePath = 'data';
    protected ?float $maxFileSize = null;
    protected ?float $minFileSize = null;
    protected array $fileFilter = [];
    protected bool $imageTransformable = true;
    protected ?bool $imageTransformKeepOriginal = null;
    protected bool $isImageOnly = false;
    protected ?Dimensions $imageDimensions = null;
    protected bool $imageCompactThumbnail = false;
    protected bool $imageOptimize = false;
    protected ?array $imageCropRatio = null;
    protected ?array $imageTransformableFileTypes = null;

    public static function make(string $key): self
    {
        return new static($key, static::FIELD_TYPE, app(UploadFormatter::class));
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
            /** @see \Illuminate\Validation\Concerns\ValidatesAttributes::validateImage() */
            $this->setFileFilter(['.jpg', '.jpeg', '.png', '.gif', '.bmp', '.svg', '.webp']);
        }

        return $this;
    }

    public function setImageCropRatio(string $ratio = null, ?array $transformableFileTypes = null): self
    {
        if ($ratio) {
            $this->imageCropRatio = explode(':', $ratio);

            $this->imageTransformableFileTypes = $transformableFileTypes
                ? $this->formatFileExtension($transformableFileTypes)
                : null;
        } else {
            $this->imageCropRatio = null;
            $this->imageTransformableFileTypes = null;
        }

        return $this;
    }

    public function setImageDimensions(Dimensions $dimensions): self
    {
        $this->imageDimensions = $dimensions;

        return $this;
    }

    public function setImageOptimize(bool $mageOptimize = true): self
    {
        $this->imageOptimize = $mageOptimize;

        return $this;
    }

    public function isImageOptimize(): bool
    {
        return $this->imageOptimize;
    }

    public function setImageCompactThumbnail(bool $compactThumbnail = true): self
    {
        $this->imageCompactThumbnail = $compactThumbnail;

        return $this;
    }

    public function setImageTransformable(bool $transformable = true, ?bool $transformKeepOriginal = null): self
    {
        $this->imageTransformable = $transformable;

        if ($transformable && ! is_null($transformKeepOriginal)) {
            $this->imageTransformKeepOriginal = $transformKeepOriginal;
        }

        return $this;
    }

    public function isImageTransformable(): bool
    {
        return $this->imageTransformable;
    }

    public function isImageTransformOriginal(): bool
    {
        return $this->imageTransformable && ! $this->isImageTransformKeepOriginal();
    }

    public function isImageTransformKeepOriginal(): bool
    {
        return $this->imageTransformKeepOriginal ?? config('sharp.uploads.transform_keep_original_image', true);
    }

    public function imageTransformableFileTypes(): ?array
    {
        return $this->imageTransformableFileTypes;
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
        return $this->imageCropRatio;
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
            'ratioX' => $this->imageCropRatio ? (int) $this->imageCropRatio[0] : null,
            'ratioY' => $this->imageCropRatio ? (int) $this->imageCropRatio[1] : null,
            'transformable' => $this->imageTransformable,
            'transformableFileTypes' => $this->imageTransformableFileTypes,
            'transformKeepOriginal' => $this->isImageTransformKeepOriginal(),
            'compactThumbnail' => (bool) $this->imageCompactThumbnail,
            'storageBasePath' => $this->storageBasePath,
            'storageDisk' => $this->storageDisk,
            'shouldOptimizeImage' => $this->imageOptimize,
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

    /** @deprecated  */
    public function setCropRatio(string $ratio = null, ?array $transformableFileTypes = null): self
    {
        return $this->setImageCropRatio($ratio, $transformableFileTypes);
    }

    /** @deprecated */
    public function shouldOptimizeImage(bool $shouldOptimizeImage = true): self
    {
        return $this->setImageOptimize($shouldOptimizeImage);
    }

    /** @deprecated  */
    public function setCompactThumbnail(bool $compactThumbnail = true): self
    {
        return $this->setImageCompactThumbnail($compactThumbnail);
    }

    /** @deprecated  */
    public function setTransformable(bool $transformable = true, ?bool $transformKeepOriginal = null): self
    {
        return $this->setImageTransformable($transformable, $transformKeepOriginal);
    }

    /** @deprecated */
    public function setFileFilterImages(): self
    {
        return $this->setImageOnly();
    }
}
