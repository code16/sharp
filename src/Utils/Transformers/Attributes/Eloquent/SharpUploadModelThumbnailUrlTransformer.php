<?php

namespace Code16\Sharp\Utils\Transformers\Attributes\Eloquent;

use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;

/**
 * Transforms a SharpUploadModel instance in a thumbnail URL.
 * Used in SharpShow, and SharpEntityList (with ->renderAsImageTag()).
 */
class SharpUploadModelThumbnailUrlTransformer implements SharpAttributeTransformer
{
    protected ?int $width;
    protected ?int $height;
    protected array $filters;
    protected bool $renderAsImageTag = false;

    public function __construct(int $width = null, int $height = null, array $filters = [])
    {
        $this->width = $width;
        $this->height = $height;
        $this->filters = $filters;
    }

    public function renderAsImageTag(bool $renderAsImageTag = true): self
    {
        $this->renderAsImageTag = $renderAsImageTag;

        return $this;
    }

    /**
     * Transform a model attribute to array (json-able).
     *
     * @param mixed  $value
     * @param object $instance
     * @param string $attribute
     *
     * @throws SharpException
     *
     * @return mixed
     */
    public function apply($value, $instance = null, $attribute = null)
    {
        if (!$instance->$attribute) {
            return null;
        }

        if (!$instance->$attribute instanceof SharpUploadModel) {
            throw new SharpException("[$attribute] must be an instance of SharpUploadModel");
        }

        $url = $instance->$attribute->thumbnail($this->width, $this->height, $this->filters);

        return $this->renderAsImageTag
            ? sprintf('<img src="%s" alt="" class="img-fluid">', $url)
            : $url;
    }
}
