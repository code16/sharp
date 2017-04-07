<?php

namespace Code16\Sharp\Form\Layout;

class FormLayoutField implements HasLayout
{
    /**
     * @var string
     */
    protected $fieldKey;

    /**
     * @var int
     */
    protected $size = 12;

    /**
     * @var int
     */
    protected $sizeXS = 12;

    /**
     * @param string $fieldKey
     */
    function __construct(string $fieldKey)
    {
        if(strpos($fieldKey, "|")) {
            list($this->fieldKey, $sizes) = explode("|", $fieldKey);

            if(strpos($fieldKey, ",")) {
                list($this->size, $this->sizeXS) = collect(explode(",", $sizes))->map(function($size) {
                    return (int)$size;
                });

            } else {
                $this->size = (int)$sizes;
            }

        } else {
            $this->fieldKey = $fieldKey;
        }
    }

    /**
     * @return array
     */
    function toArray(): array
    {
        return [
            "key" => $this->fieldKey,
            "size" => $this->size,
            "sizeXS" => $this->sizeXS
        ];
    }
}