<?php

namespace Code16\Sharp\Utils\Layout;

use Code16\Sharp\Form\Layout\HasFieldRows;

abstract class LayoutColumn
{
    use HasFieldRows;

    public function __construct(
        protected int $size,
        protected LayoutFieldFactory $layoutFieldFactory
    ) {
    }

    public function toArray(): array
    {
        return array_merge(
            ['size' => $this->size],
            $this->fieldsToArray(),
        );
    }
}
