<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Thumbnails;

use Closure;
use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Intervention\Image\Drivers\Imagick\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\Encoders\AvifEncoder;
use Intervention\Image\Encoders\BmpEncoder;
use Intervention\Image\Encoders\GifEncoder;
use Intervention\Image\Encoders\HeicEncoder;
use Intervention\Image\Encoders\Jpeg2000Encoder;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
use Intervention\Image\Encoders\TiffEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Exceptions\DecoderException;
use Intervention\Image\Exceptions\EncoderException;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\EncoderInterface;

class Thumbnail
{
    use Conditionable;

    protected ImageManager $imageManager;
    protected ?SharpUploadModel $uploadModel = null;
    protected ?string $encoderClass = null;
    protected int $quality = 90;
    protected bool $appendTimestamp = false;
    protected ?Closure $afterClosure = null;
    protected ?array $transformationFilters = null;

    public function __construct(ImageManager $imageManager = null)
    {
        $this->imageManager = $imageManager ?: new ImageManager(new Driver());
    }

    public function for(SharpUploadModel $model): self
    {
        $this->uploadModel = $model;

        return $this;
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

    public function forceWebpEncoder(): self
    {
        $this->encoderClass = WebpEncoder::class;

        return $this;
    }

    public function forcePngEncoder(): self
    {
        $this->encoderClass = PngEncoder::class;

        return $this;
    }

    public function forceJpegEncoder(): self
    {
        $this->encoderClass = JpegEncoder::class;

        return $this;
    }

    public function forceGifEncoder(): self
    {
        $this->encoderClass = GifEncoder::class;

        return $this;
    }

    public function forceAvifEncoder(): self
    {
        $this->encoderClass = AvifEncoder::class;

        return $this;
    }

    public function make(?int $width, ?int $height = null, array $filters = []): ?string
    {
        if (! $this->uploadModel->disk || ! $this->uploadModel->file_name) {
            return null;
        }

        $thumbnailDisk = Storage::disk(config('sharp.uploads.thumbnails_disk', 'public'));

        $thumbDirNameAppender = ($this->transformationFilters ? '_'.md5(serialize($this->transformationFilters)) : '')
            .(sizeof($filters) ? '_'.md5(serialize($filters)) : '')
            ."_q-$this->quality";

        $extension = $this->resolveThumbnailExtension();

        $thumbnailPath = sprintf(
            '%s/%s/%s-%s%s/%s.%s',
            config('sharp.uploads.thumbnails_dir', 'thumbnails'),
            dirname($this->uploadModel->file_name),
            $width, $height, $thumbDirNameAppender,
            str(basename($this->uploadModel->file_name))->beforeLast('.'),
            $extension
        );

        // Strip double /
        $thumbnailPath = Str::replace('//', '/', $thumbnailPath);

        $wasCreated = ! $thumbnailDisk->exists($thumbnailPath);
        $url = $this->generateThumbnail($thumbnailPath, $width, $height, $filters);

        if ($closure = $this->afterClosure) {
            $closure($wasCreated, $thumbnailPath, $thumbnailDisk);
        }

        return $url;
    }

    public function destroyAllThumbnails(): void
    {
        $thumbnailDisk = Storage::disk(config('sharp.uploads.thumbnails_disk', 'public'));
        $thumbnailPath = config('sharp.uploads.thumbnails_dir', 'thumbnails');
        $destinationRelativeBasePath = dirname($this->uploadModel->file_name);

        $thumbnailDisk->deleteDirectory("$thumbnailPath/$destinationRelativeBasePath");
    }

    private function generateThumbnail(string $thumbnailPath, ?int $width, ?int $height, array $filters): ?string {
        if ($width == 0) {
            $width = null;
        }
        if ($height == 0) {
            $height = null;
        }

        $sourceDisk = $this->uploadModel->disk;
        $sourceRelativeFilePath = $this->uploadModel->file_name;
        $thumbnailDisk = Storage::disk(config('sharp.uploads.thumbnails_disk', 'public'));

        if (! $thumbnailDisk->exists($thumbnailPath)) {
            // Create thumbnail directories if needed
            if (! $thumbnailDisk->exists(dirname($thumbnailPath))) {
                $thumbnailDisk->makeDirectory(dirname($thumbnailPath));
            }

            try {
                $sourceImg = $this->imageManager->read(
                    Storage::disk($sourceDisk)->get($sourceRelativeFilePath),
                );

                // Transformation filters
                if ($this->transformationFilters) {
                    if ($rotate = Arr::get($this->transformationFilters, 'rotate.angle')) {
                        $sourceImg->rotate($rotate);
                    }

                    if ($cropData = Arr::get($this->transformationFilters, 'crop')) {
                        $sourceImg->crop(
                            intval(round($sourceImg->width() * $cropData['width'])),
                            intval(round($sourceImg->height() * $cropData['height'])),
                            intval(round($sourceImg->width() * $cropData['x'])),
                            intval(round($sourceImg->height() * $cropData['y'])),
                        );
                    }
                }

                // Custom filters
                $alreadyResized = false;
                foreach ($filters as $modifier => $params) {
                    $modifierInstance = $this->resolveModifierClass($modifier, $params);
                    if ($modifierInstance) {
                        $sourceImg->modify($modifierInstance);
                        $alreadyResized = $alreadyResized || $modifierInstance->resized();
                    }
                }

                // Resize if needed
                if (! $alreadyResized) {
                    $sourceImg->scaleDown($width, $height);
                }

                $thumbnailDisk->put(
                    $thumbnailPath,
                    $sourceImg->encode($this->resolveEncoder())
                );
            } catch (FileNotFoundException|EncoderException|DecoderException) {
                return null;
            }
        }

        return $thumbnailDisk->url($thumbnailPath)
            .($this->appendTimestamp ? '?'.$thumbnailDisk->lastModified($thumbnailPath) : '');
    }

    private function resolveModifierClass(string $class, array $params): ?ThumbnailModifier
    {
        if (! Str::contains($class, '\\')) {
            $class = 'Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\\'.ucfirst($class).'Modifier';

            // Backward compatibility
            if (! class_exists($class)) {
                $class = 'Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\\'.ucfirst($class).'Filter';
            }
        }

        return class_exists($class) ? new $class($params) : null;
    }

    private function resolveEncoder(): EncoderInterface
    {
        if ($this->encoderClass) {
            if (class_exists($this->encoderClass)
                && class_implements($this->encoderClass, EncoderInterface::class)
            ) {
                $class = $this->encoderClass;

                return new $class(quality: $this->quality);
            }
            throw new EncoderException('Encoder class ('.$this->encoderClass.') does not exist or does not implement EncoderInterface.');
        }

        return new AutoEncoder(quality: $this->quality);
    }

    private function resolveThumbnailExtension(): string
    {
        return match ($this->encoderClass) {
            WebpEncoder::class => 'webp',
            AvifEncoder::class => 'avif',
            JpegEncoder::class => 'jpeg',
            GifEncoder::class => 'gif',
            PngEncoder::class => 'png',
            default => str($this->uploadModel->file_name)->afterLast('.'),
        };
    }
}
