<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Data\DataCollection;
use Code16\Sharp\Enums\FilterType;

final class SelectFilterData extends Data
{
    public function __construct(
        public string $key,
        public string $label,
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
        return new self(...[
            ...$filter,
            'values' => SelectFilterValueData::collection($filter['values']),
        ]);
    }
}
