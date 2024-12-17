<?php

namespace Code16\Sharp\Utils\Filters;

use ArrayAccess;
use Carbon\Carbon;

final class DateRangeFilterValue implements ArrayAccess
{
    public function __construct(
        protected Carbon $start,
        protected Carbon $end,
    ) {}

    public function getStart(): Carbon
    {
        return $this->start;
    }

    public function getEnd(): Carbon
    {
        return $this->end;
    }

    public function offsetExists(mixed $offset): bool
    {
        return property_exists($this, $offset);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->$offset;
    }

    public function offsetSet(mixed $offset, mixed $value): void {}

    public function offsetUnset(mixed $offset): void {}
}
