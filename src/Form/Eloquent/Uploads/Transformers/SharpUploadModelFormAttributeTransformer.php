<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Transformers;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Form\Eloquent\Uploads\Traits\UsesSharpUploadModel;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Intervention\Image\Exceptions\DecoderException;

class SharpUploadModelFormAttributeTransformer implements SharpAttributeTransformer
{
    use UsesSharpUploadModel;

    private bool $dynamicSharpUploadModel = false;

    public function __construct(
        protected bool $withThumbnails = true,
        protected int $thumbnailWidth = 200,
        protected int $thumbnailHeight = 200,
        protected bool $withPlayablePreview = false,
    ) {}

    public function dynamicInstance(): self
    {
        $this->dynamicSharpUploadModel = true;

        return $this;
    }

    public function apply($value, $instance = null, $attribute = null)
    {
        if ($this->dynamicSharpUploadModel) {
            // In this case, $instance is not a SharpUploadModel object, and we have to fake one first
            // This happens in an embed case: there is no SharpUploadModel object
            if (! $value || ! is_array($value)) {
                return null;
            }

            if ($value['uploaded'] ?? false) {
                return $value;
            }

            $instance = (object) [
                $attribute => static::getUploadModelClass()::make([
                    'file_name' => $value['file_name'] ?? $value['path'],
                    'filters' => $value['filters'] ?? null,
                    'mime_type' => $value['mime_type'] ?? null,
                    'disk' => $value['disk'],
                    'size' => $value['size'] ?? null,
                ]),
            ];

            return [
                ...$this->transformUpload($instance->$attribute),
                ...($value['transformed'] ?? false) ? ['transformed' => true] : [],
            ];
        }

        if (! $instance->$attribute) {
            return null;
        }

        if ($instance instanceof Model
            && $instance->isRelation($attribute)
            && $instance->$attribute() instanceof MorphMany) {
            // We are handling a list of uploads
            return $instance->$attribute
                ->map(function ($upload) {
                    $array = $this->transformUpload($upload);
                    $fileAttrs = [
                        'name',
                        'path',
                        'disk',
                        'thumbnail',
                        'playable_preview_url',
                        'size',
                        'filters',
                        'mime_type',
                    ];

                    return array_merge(
                        ['file' => Arr::only($array, $fileAttrs) ?: null],
                        Arr::except($array, $fileAttrs),
                    );
                })
                ->all();
        }

        return $this->transformUpload($instance->$attribute);
    }

    protected function transformUpload(SharpUploadModel $upload): array
    {
        return [
            ...$upload->file_name
                ? [
                    'name' => basename($upload->file_name),
                    'path' => $upload->file_name,
                    'disk' => $upload->disk,
                    'mime_type' => $upload->mime_type,
                    'thumbnail' => $this->getThumbnailUrl($upload),
                    'playable_preview_url' => $this->getPlayableMediaUrl($upload),
                    'size' => $upload->size,
                ]
                : [],
            ...$upload->custom_properties ?? [], // Including filters
            'id' => $upload->id,
        ];
    }

    private function getThumbnailUrl(SharpUploadModel $upload): ?string
    {
        if (! $this->withThumbnails) {
            return null;
        }

        // prevent generating thumbnail for non-image files
        if ($upload->mime_type && ! str($upload->mime_type)->startsWith('image/')) {
            return null;
        }

        try {
            return $this->getRelativeUrlIfPossible(
                $upload->thumbnail($this->thumbnailWidth, $this->thumbnailHeight)
            );
        } catch (DecoderException) {
            return null;
        }
    }

    private function getPlayableMediaUrl(SharpUploadModel $upload): ?string
    {
        if (! $this->withPlayablePreview) {
            return null;
        }

        if ($upload->mime_type && ! str($upload->mime_type)->startsWith(['video/', 'audio/'])) {
            return null;
        }

        return $this->getRelativeUrlIfPossible(
            $upload->playablePreviewUrl()
        );
    }

    private function getRelativeUrlIfPossible(string $url): ?string
    {
        // Return relative URL if possible, to avoid CORS issues in multidomain case.
        return Str::startsWith($url, config('app.url'))
            ? Str::after($url, config('app.url'))
            : $url;
    }
}
