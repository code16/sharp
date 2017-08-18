<?php

namespace Code16\Sharp\EntityList\Eloquent\Transformers;

use Code16\Sharp\Exceptions\SharpException;
use Code16\Sharp\Form\Eloquent\Uploads\SharpUploadModel;
use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;

/**
 * Special SharpAttributeTransformer to handle SharpUploadModel
 * transformation in an EntityList
 */
class SharpUploadModelAttributeTransformer implements SharpAttributeTransformer
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
     * @param $instance
     * @param string $attribute
     * @return mixed
     * @throws SharpException
     */
    function apply($instance, string $attribute)
    {
        if(!$instance->$attribute) {
            return null;
        }

        if(!$instance->$attribute instanceof SharpUploadModel) {
            throw new SharpException("[$attribute] mist be an instance of SharpUploadModel");
        }

        return '<img src="'
            . $instance->$attribute->thumbnail($this->width, $this->height, $this->filters)
            . '" alt="" class="img-fluid">';
    }
}