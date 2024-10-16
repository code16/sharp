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
use Intervention\Image\Encoders\AvifEncoder;
use Intervention\Image\Encoders\FilePathEncoder;
use Intervention\Image\Encoders\GifEncoder;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Encoders\PngEncoder;
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
    protected array $modifiers = [];

    public function __construct(?SharpUploadModel $model = null)
    {
        $this->uploadModel = $model;
        $this->transformationFilters = $model?->filters;
        $this->imageManager = new ImageManager(new Driver());
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

    public function toWebp(): self
    {
        $this->encoderClass = WebpEncoder::class;

        return $this;
    }

    public function toPng(): self
    {
        $this->encoderClass = PngEncoder::class;

        return $this;
    }

    public function toJpeg(): self
    {
        $this->encoderClass = JpegEncoder::class;

        return $this;
    }

    public function toGif(): self
    {
        $this->encoderClass = GifEncoder::class;

        return $this;
    }

    public function toAvif(): self
    {
        $this->encoderClass = AvifEncoder::class;

        return $this;
    }

    public function addModifier(ThumbnailModifier|string $modifier): self
    {
        $this->modifiers[] = $modifier;

        return $this;
    }

    public function make(?int $width = null, ?int $height = null): ?string
    {
        if (! $this->uploadModel || ! $this->uploadModel->disk || ! $this->uploadModel->file_name) {
            return null;
        }

        $thumbnailDisk = Storage::disk(sharp()->config()->get('uploads.thumbnails_disk'));
        $thumbnailPath = $this->resolveThumbnailPath($width, $height);
        $wasCreated = ! $thumbnailDisk->exists($thumbnailPath);
        $url = $this->generateThumbnail($thumbnailPath, $width, $height);

        if ($closure = $this->afterClosure) {
            $closure($wasCreated, $thumbnailPath, $thumbnailDisk);
        }

        return $url;
    }

    private function generateThumbnail(string $thumbnailPath, ?int $width, ?int $height): ?string
    {
        if ($width == 0) {
            $width = null;
        }
        if ($height == 0) {
            $height = null;
        }

        $sourceDisk = $this->uploadModel->disk;
        $sourceRelativeFilePath = $this->uploadModel->file_name;
        $thumbnailDisk = Storage::disk(sharp()->config()->get('uploads.thumbnails_disk'));

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

                // Custom modifiers
                $alreadyResized = false;
                foreach ($this->modifiers as $modifier) {
                    $modifierInstance = $this->resolveModifierClass($modifier);
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

    private function resolveModifierClass(ThumbnailModifier|string $modifier): ?ThumbnailModifier
    {
        if ($modifier instanceof ThumbnailModifier) {
            return $modifier;
        }

        if (! Str::contains($modifier, '\\')) {
            $modifier = 'Code16\Sharp\Form\Eloquent\Uploads\Thumbnails\\'.ucfirst($modifier).'Modifier';
        }

        if (class_exists($modifier) && is_subclass_of($modifier, ThumbnailModifier::class)) {
            return new $modifier();
        }

        return null;
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

        return new FilePathEncoder(path: $this->uploadModel->file_name, quality: $this->quality);
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

    private function resolveThumbnailPath(int $width = null, int $height = null): string
    {
        $thumbDirNameAppender = sprintf(
            '%s%s_q-%s',
            $this->transformationFilters ? '_'.md5(serialize($this->transformationFilters)) : '',
            sizeof($this->modifiers) ? '_'.md5(serialize($this->modifiers)) : '',
            $this->quality
        );

        $thumbnailPath = sprintf(
            '%s/%s/%s-%s%s/%s.%s',
            sharp()->config()->get('uploads.thumbnails_dir'),
            dirname($this->uploadModel->file_name),
            $width,
            $height,
            $thumbDirNameAppender,
            Str::of(basename($this->uploadModel->file_name))->beforeLast('.'),
            $this->resolveThumbnailExtension()
        );

        // Strip double /
        return Str::replace('//', '/', $thumbnailPath);
    }
}
