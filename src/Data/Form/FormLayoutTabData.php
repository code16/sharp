<?php

namespace Code16\Sharp\Data\Form;

use Code16\Sharp\Data\Data;

/**
 * @internal
 */
final class FormLayoutTabData extends Data
{
    public function __construct(
        public string $title,
        /** @var FormLayoutColumnData[] */
        public array $columns,
    ) {}

    public static function from(array $tab): self
    {
        return new self(
            title: $tab['title'],
            columns: FormLayoutColumnData::collection($tab['columns']),
        );
    }
}
