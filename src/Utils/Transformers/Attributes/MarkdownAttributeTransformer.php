<?php

namespace Code16\Sharp\Utils\Transformers\Attributes;

use Code16\Sharp\Utils\Transformers\SharpAttributeTransformer;

class MarkdownAttributeTransformer implements SharpAttributeTransformer
{
    protected bool $handleImages = false;
    protected ?int $imageWidth;
    protected ?int $imageHeight;
    protected ?array $imageFilters;

    public function handleImages(int $width = null, int $height = null, array $filters = []): self
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

        $html = (new \Parsedown())
            ->setBreaksEnabled(true)
            ->text($instance->$attribute);
        
        if($this->handleImages) {
            return sharp_markdown_embedded_files(
                $html, "", $this->imageWidth, $this->imageHeight, $this->imageFilters
            );
        }

        return $html;
    }
}