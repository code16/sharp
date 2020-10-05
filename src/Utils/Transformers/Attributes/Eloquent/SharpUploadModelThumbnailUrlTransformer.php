<?php

namespace Code16\Sharp\Utils\Transformers\Attributes\Eloquent;

use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;

/**
 * Transforms a SharpUploadModel instance in a thumbnail URL.
 * Used in SharpShow, meant to be used in SharpEntityList (should replace SharpUploadModelAttributeTransformer)
 */
class SharpUploadModelThumbnailUrlTransformer implements SharpAttributeTransformer
{
    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var array
     */
    protected $filters;

    /**
     * @param int|null $width
     * @param int|null $height
     * @param array $filters
     */
    public function __construct(int $width = null, int $height = null, array $filters = [])
    {
        $this->width = $width;
        $this->height = $height;
        $this->filters = $filters;
    }

    /**
     * Transform a model attribute to array (json-able).
     *
     * @param mixed $value
     * @param object $instance
     * @param string $attribute
     * @return mixed
     * @throws SharpException
     */
    function apply($value, $instance = null, $attribute = null)
    {
        if(!$instance->$attribute) {
            return null;
        }

        if(!$instance->$attribute instanceof SharpUploadModel) {
            throw new SharpException("[$attribute] must be an instance of SharpUploadModel");
        }

        return $instance->$attribute->thumbnail($this->width, $this->height, $this->filters);
    }
}