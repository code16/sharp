<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Enums\FilterType;
use Spatie\TypeScriptTransformer\Attributes\LiteralTypeScriptType;
use Spatie\TypeScriptTransformer\Attributes\Optional;
use Spatie\TypeScriptTransformer\Attributes\TypeScriptType;

final class SelectFilterData extends Data
{
    #[Optional]
    /** @var int|string|array<int|string> */
    public mixed $value;

    public function __construct(
        public string $key,
        public ?string $label,
        #[LiteralTypeScriptType('"'.FilterType::Select->value.'"')]
        public FilterType $type,
        /** @var int|string|array<int|string> */
        public mixed $default,
        public bool $multiple,
        public bool $required,
        /** @var DataCollection<SelectFilterValueData> */
        public DataCollection $values,
        public bool $master,
        public bool $searchable,
        /** string[] */
        public array $searchKeys,
        public string $template,
    ) {
    }

    public static function from(array $filter): self
    {
        $filter = [
            ...$filter,
            'values' => SelectFilterValueData::collection($filter['values']),
        ];

        return new self(...$filter);
    }
}
