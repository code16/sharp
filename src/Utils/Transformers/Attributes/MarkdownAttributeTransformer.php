<?php

namespace Code16\Sharp\Utils\Transformers\Attributes;

use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;

class MarkdownAttributeTransformer implements SharpAttributeTransformer
{
    /** @var bool */
    protected $handleImages = false;

    /** @var int */
    protected $imageWidth;

    /** @var int */
    protected $imageHeight;

    /** @var array */
    protected $imageFilters;

    /**
     * @param int|null $width
     * @param int|null $height
     * @param array $filters
     * @return MarkdownAttributeTransformer
     */
    public function handleImages(int $width = null, int $height = null, array $filters = [])
    {
        $this->handleImages = true;
        $this->imageWidth = $width;
        $this->imageHeight = $height;
        $this->imageFilters = $filters;

        return $this;
    }

    /**
     * Transform a model attribute to array (json-able).
     *
     * @param mixed $value
     * @param object $instance
     * @param string $attribute
     * @return mixed
     */
    function apply($value, $instance = null, $attribute = null)
    {
        if(!$instance->$attribute) {
            return null;
        }

        $html = (new \Parsedown())->parse($instance->$attribute);

        if($this->handleImages) {
            return sharp_markdown_thumbnails(
                $html, "", $this->imageWidth, $this->imageHeight, $this->imageFilters
            );
        }

        return $html;
    }
}