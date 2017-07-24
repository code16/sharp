<?php

namespace Code16\Sharp\EntityList\Layout;

class EntityListLayoutColumn
{
    /**
     * @var string
     */
    protected $key;

    /**
     * @var int
     */
    protected $size;

    /**
     * @var int|null
     */
    protected $sizeXS;

    /**
     * @var boolean
     */
    protected $largeOnly = false;

    /**
     * @param string $key
     * @param int $size
     * @param null $sizeXS
     */
    public function __construct(string $key, int $size, $sizeXS = null)
    {
        $this->key = $key;
        $this->size = $size;
        $this->sizeXS = $sizeXS ?: $size;
    }

    /**
     * @param bool $largeOnly
     */
    public function setLargeOnly($largeOnly = true)
    {
        $this->largeOnly = $largeOnly;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            "key" => $this->key,
            "size" => $this->size,
            "sizeXS" => $this->sizeXS,
            "hideOnXS" => $this->largeOnly,
        ];
    }
}