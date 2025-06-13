<?php

namespace Code16\Sharp\Data\Filters;

use Code16\Sharp\Data\Data;

/**
 * @internal
 */
final class DateRangePresetData extends Data
{
    public function __construct(
        public ?string $label,
        public ?string $start,
        public ?string $end,
    ) {}

    public static function from(array $filter): self
    {
        return new self(...$filter);
    }
}
