<?php

namespace Code16\Sharp\Data;

/**
 * @internal
 */
final class LayoutFieldData extends Data
{
    public function __construct(
        public string $key,
        public int $size,
        /** @var array<array<LayoutFieldData>> */
        public ?array $item = null,
    ) {}

    public static function from(array $field): self
    {
        $field = [
            ...$field,
            'item' => isset($field['item'])
                ? collect($field['item'])->map(fn (array $row) => collect($row)->map(fn (array $itemField) => LayoutFieldData::from($itemField)
                )
                )->toArray()
                : null,
        ];

        return new self(...$field);
    }
}
