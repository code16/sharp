<?php

namespace Code16\Sharp\Form\Eloquent\Uploads\Transformers;

use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Form\Eloquent\Uploads\Traits\UsesSharpUploadModel;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class SharpUploadModelFormAttributeTransformer implements SharpAttributeTransformer
{
    use UsesSharpUploadModel;

    protected bool $withThumbnails;
    protected int $thumbnailWidth;
    protected int $thumbnailHeight;
    private bool $dynamicSharpUploadModel = false;

    public function __construct(bool $withThumbnails = true, int $thumbnailWidth = 200, int $thumbnailHeight = 200)
    {
        $this->withThumbnails = $withThumbnails;
        $this->thumbnailWidth = $thumbnailWidth;
        $this->thumbnailHeight = $thumbnailHeight;
    }

    public function dynamicInstance(): self
    {
        $this->dynamicSharpUploadModel = true;

        return $this;
    }

    /**
     * Transform a model attribute to array (json-able).
     *
     * @param  $value
     * @param  $instance
     * @param  string  $attribute
     * @return mixed
     */
    public function apply($value, $instance = null, $attribute = null)
    {
        if ($this->dynamicSharpUploadModel) {
            // In this case, $instance is not a SharpUploadModel object, and we have to fake one first
            // This happens in an embed case: there is no SharpUploadModel object
            if (! $value || ! is_array($value)) {
                return null;
            }

            $instance = (object) [
                $attribute => static::getUploadModelClass()::make([
                    'file_name' => $value['file_name'] ?? $value['path'],
                    'filters' => $value['filters'],
                    'disk' => $value['disk'],
                    'size' => $value['size'],
                ]),
            ];
        }

        if (! $instance->$attribute) {
            return null;
        }

        if (method_exists($instance, $attribute) && $instance->$attribute() instanceof MorphMany) {
            // We are handling a list of uploads
            return $instance->$attribute
                ->map(function ($upload) {
                    $array = $this->transformUpload($upload);
                    $fileAttrs = ['name', 'path', 'disk', 'thumbnail', 'size', 'filters'];

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
        return array_merge(
            $upload->file_name
                ? [
                    'name' => basename($upload->file_name),
                    'path' => $upload->file_name,
                    'disk' => $upload->disk,
                    'thumbnail' => $this->getThumbnailUrl($upload),
                    'size' => $upload->size,
                ]
                : [],
            $upload->custom_properties ?? [], // Including filters
            ['id' => $upload->id],
        );
    }

    private function getThumbnailUrl(SharpUploadModel $upload): ?string
    {
        if (! $this->withThumbnails) {
            return null;
        }

        $url = $upload->thumbnail($this->thumbnailWidth, $this->thumbnailHeight);

        // Return relative URL if possible, to avoid CORS issues in multidomain case.
        return Str::startsWith($url, config('app.url'))
            ? Str::after($url, config('app.url'))
            : $url;
    }
}
