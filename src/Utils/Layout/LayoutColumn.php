<?php

namespace Code16\Sharp\Utils\Layout;

use Code16\Sharp\Form\Layout\HasFieldRows;

abstract class LayoutColumn
{
    use HasFieldRows;

    /**
     * @var int
     */
    protected $size;

    /**
     * @param int $size
     */
    function __construct(int $size)
    {
        $this->size = $size;
    }

    /**
     * @inheritDoc
     */
    function toArray(): array
    {
        return array_merge(
            ["size" => $this->size],
            $this->fieldsToArray()
        );
    }
}