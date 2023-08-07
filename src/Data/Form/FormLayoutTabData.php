<?php

namespace Code16\Sharp\Data\Form;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;

final class FormLayoutTabData extends Data
{
    public function __construct(
        public string $title,
        /** @var DataCollection<FormLayoutColumnData> */
        public DataCollection $columns,
    ) {
    }

    public static function from(array $tab): self
    {
        return new self(
            title: $tab['title'],
            columns: FormLayoutColumnData::collection($tab['columns']),
        );
    }
}
