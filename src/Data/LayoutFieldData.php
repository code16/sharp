<?php

namespace Code16\Sharp\Data;


use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Data\NotificationData;
use Spatie\TypeScriptTransformer\Attributes\Optional;

final class LayoutFieldData extends Data
{
    public function __construct(
        public string $key,
        public int $size,
        public int $sizeXS,
        /** @var DataCollection<string,ShowLayoutFieldData> */
        public ?array $item = null,
    ) {
    }

    public static function from(array $field): self
    {
        $field = [
            ...$field,
            'item' => isset($field['item'])
                ? collect($field['item'])->map(fn(array $row) =>
                    collect($row)->map(fn (array $itemField) =>
                        LayoutFieldData::from($itemField)
                    )
                )->toArray()
                : null,
        ];

        return new self(...$field);
    }
}
