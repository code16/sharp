<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;
use Code16\Sharp\Enums\FilterType;

final class DateRangeFilterData extends Data
{
    public function __construct(
        public string $key,
        public string $label,
        public FilterType $type,
        public ?DateRangeFilterValueData $default,
        public bool $required,
        public bool $mondayFirst,
        public string $displayFormat
    ) {
    }
    
    public static function from(array $filter): self
    {
        $filter = [
            ...$filter,
            'default' => $filter['default']
                ? new DateRangeFilterValueData(...$filter['default'])
                : null,
        ];
        
        return new self(...$filter);
    }
}
