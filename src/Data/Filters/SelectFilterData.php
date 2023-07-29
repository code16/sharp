<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\FilterType;
use Code16\Sharp\Utils\Filters\SelectRequiredFilter;

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
        /** @var SelectFilterValueData[] */
        public array $values,
        public bool $master,
        public bool $searchable,
        /** string[] */
        public array $searchKeys,
        public string $template,
    ) {
    }
}
