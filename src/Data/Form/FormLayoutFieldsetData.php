<?php

namespace Code16\Sharp\Data\Form;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\LayoutFieldData;

final class FormLayoutFieldsetData extends Data
{
    public function __construct(
        public string $legend,
        /** @var array<array<LayoutFieldData>> */
        public array $fields,
    ) {}

    public static function from(array $fieldset): self
    {
        $fieldset = [
            ...$fieldset,
            'fields' => collect($fieldset['fields'])->map(fn (array $row) => collect($row)->map(fn (array $field) => LayoutFieldData::from($field)
            )
            )->toArray(),
        ];

        return new self(...$fieldset);
    }
}
