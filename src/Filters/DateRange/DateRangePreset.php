<?php

namespace Code16\Sharp\Filters\DateRange;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class DateRangePreset implements Arrayable
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

    public static function make(Carbon $start, Carbon $end, string $label): self
    {
        return new static($start, $end, $label);
    }

    public static function today(): self
    {
        return new static(
            Carbon::today(),
            Carbon::today(),
            __('sharp::filters.daterange.preset.today'),
        );
    }

    public static function yesterday(): self
    {
        return new static(
            Carbon::yesterday(),
            Carbon::yesterday(),
            __('sharp::filters.daterange.preset.yesterday'),
        );
    }

    public static function last7days(): self
    {
        return new static(
            Carbon::today()->subDays(6),
            Carbon::today(),
            __('sharp::filters.daterange.preset.last_7_days'),
        );
    }

    public static function last30days(): self
    {
        return new static(
            Carbon::today()->subDays(29),
            Carbon::today(),
            __('sharp::filters.daterange.preset.last_30_days'),
        );
    }

    public static function last365days(): self
    {
        return new static(
            Carbon::today()->subDays(364),
            Carbon::today(),
            __('sharp::filters.daterange.preset.last_365_days'),
        );
    }

    public static function thisMonth(): self
    {
        return new static(
            Carbon::today()->startOfMonth(),
            Carbon::today()->endOfMonth(),
            __('sharp::filters.daterange.preset.this_month'),
        );
    }

    public static function lastMonth(): self
    {
        return new static(
            Carbon::today()->subMonth()->startOfMonth(),
            Carbon::today()->subMonth()->endOfMonth(),
            __('sharp::filters.daterange.preset.last_month'),
        );
    }

    public static function thisYear(): self
    {
        return new static(
            Carbon::today()->startOfYear(),
            Carbon::today()->endOfYear(),
            __('sharp::filters.daterange.preset.this_year'),
        );
    }

    public static function lastYear(): self
    {
        return new static(
            Carbon::today()->subYear()->startOfYear(),
            Carbon::today()->subYear()->endOfYear(),
            __('sharp::filters.daterange.preset.last_year'),
        );
    }

    public function toArray()
    {
        return [
            'label' => $this->label,
            'start' => $this->start->format('Y-m-d'),
            'end' => $this->end->format('Y-m-d'),
        ];
    }
}
