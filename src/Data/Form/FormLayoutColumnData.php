<?php

namespace Code16\Sharp\Data\Form;


use Code16\Sharp\Data\Data;

final class FormLayoutColumnData extends Data
{
    public function __construct(
        public bool $tabbed,
    ) {
    }

    public static function from(array $config): self
    {
        return new self(
        );
    }
}
