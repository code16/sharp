<?php

namespace Code16\Sharp\Utils\Filters;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

final class DateRangePreset implements Arrayable
{
    public function __construct(
        protected Carbon $start,
        protected Carbon $end,
        protected string $label,
    ) {}

    public function getStart(): Carbon
    {
        return $this->start;
    }

    public function getEnd(): Carbon
    {
        return $this->end;
    }

    public function toArray()
    {
        return [
            'label' => $this->label,
        ];
    }
}
