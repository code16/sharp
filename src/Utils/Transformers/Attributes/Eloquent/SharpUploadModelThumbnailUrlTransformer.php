<?php

namespace Code16\Sharp\Utils\Transformers\Attributes\Eloquent;

use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;
use Illuminate\Support\Arr;

/**
 * Transforms a SharpUploadModel instance in a thumbnail URL.
 * Used in SharpShow, and SharpEntityList (with ->renderAsImageTag()).
 */
class SharpUploadModelThumbnailUrlTransformer implements SharpAttributeTransformer
{
    protected bool $renderAsImageTag = false;

    public function __construct(
        protected ?int $width = null,
        protected ?int $height = null,
        protected array $filters = []
    ) {}

    public function renderAsImageTag(bool $renderAsImageTag = true): self
    {
        $this->renderAsImageTag = $renderAsImageTag;

        return $this;
    }

    /**
     * Transform a model attribute to array (json-able).
     *
     * @param  mixed  $value
     * @param  object  $instance
     * @param  string  $attribute
     * @return mixed
     *
     * @throws SharpException
     */
    public function apply($value, $instance = null, $attribute = null)
    {
        $upload = $instance->$attribute;

        if (! $upload) {
            return null;
        }

        if (! $upload instanceof SharpUploadModel) {
            throw new SharpException("[$attribute] must be an instance of SharpUploadModel");
        }

        $url = $upload->thumbnail($this->width, $this->height, $this->filters);

        return $this->renderAsImageTag
            ? sprintf(
                '<img src="%s" alt="" style="object-fit: contain; %s">',
                $url,
                $upload->mime_type === 'image/svg+xml'
                    ? Arr::toCssStyles([
                        "width: {$this->width}px" => $this->width,
                        "height: {$this->height}px" => $this->height,
                    ])
                    : ''
            )
            : $url;
    }
}
