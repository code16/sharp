<?php

namespace Code16\Sharp\EntityList\Eloquent\Transformers;

use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;

class EloquentEntityListUploadTransformer implements SharpAttributeTransformer
{
    /**
     * @var string
     */
    protected $labelAttribute;

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
     * @param string $labelAttribute
     * @param int|null $width
     * @param int|null $height
     * @param array $filters
     */
    public function __construct(string $labelAttribute, int $width = null, int $height = null, array $filters = [])
    {
        $this->labelAttribute = $labelAttribute;
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
     */
    function apply($instance, string $attribute)
    {
        if(!$instance->$attribute) {
            return null;
        }

        return '<img src="'
            . $instance->$attribute->thumbnail($this->width, $this->height, $this->filters)
            . '" alt="" class="img-fluid">';
    }
}