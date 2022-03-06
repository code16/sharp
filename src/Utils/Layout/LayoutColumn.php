<?php

namespace Code16\Sharp\Utils\Layout;

use Code16\Sharp\Form\Layout\HasFieldRows;

abstract class LayoutColumn
{
    use HasFieldRows;

    protected int $size;

    public function __construct(int $size)
    {
        $this->size = $size;
    }
    
    public function setSize(int $size)
    {
        $this->size = $size;
    }

    public function toArray(): array
    {
        return array_merge(
            ['size' => $this->size],
            $this->fieldsToArray(),
        );
    }
}
