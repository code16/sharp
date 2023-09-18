<?php

namespace Code16\Sharp\Data\Show;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\LayoutFieldData;
use Code16\Sharp\Data\NotificationData;

final class ShowLayoutColumnData extends Data
{
    public function __construct(
        public int $size,
        /** @var array<array<LayoutFieldData>> */
        public array $fields,
    ) {
    }

    public static function from(array $column): self
    {

        $column = [
            ...$column,
            'fields' => collect($column['fields'])->map(fn (array $row) =>
                collect($row)->map(fn (array $field) =>
                    LayoutFieldData::from($field)
                )
            )->toArray(),
        ];

        return new self(...$column);
    }
}
