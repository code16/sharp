<?php

namespace Code16\Sharp\Utils\Layout;

abstract class LayoutField
{
    /** @var string */
    protected $fieldKey;

    /** @var int */
    protected $size = 12;

    /** @var int */
    protected $sizeXS = 12;

    /** @var array */
    protected $itemLayout;

    /**
     * @param string $fieldKey
     * @param \Closure|null $subLayoutCallback
     */
    function __construct(string $fieldKey, \Closure $subLayoutCallback = null)
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

        if($subLayoutCallback) {
            $itemFormLayout = $this->getLayoutColumn();
            $subLayoutCallback($itemFormLayout);
            $this->itemLayout = $itemFormLayout->toArray()["fields"];
        }
    }

    /**
     * @return LayoutColumn
     */
    protected abstract function getLayoutColumn();

    /**
     * @return array
     */
    public function toArray(): array
    {
        return array_merge(
            [
                "key" => $this->fieldKey,
                "size" => $this->size,
                "sizeXS" => $this->sizeXS
            ],
            $this->itemLayout 
                ? ["item" => $this->itemLayout] 
                : []
        );
    }
}