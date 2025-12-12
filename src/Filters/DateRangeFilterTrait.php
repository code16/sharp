<?php

namespace Code16\Sharp\Filters;

use Carbon\Carbon;
use Code16\Sharp\Enums\FilterType;
use Code16\Sharp\Filters\DateRange\DateRangeFilterValue;
use Code16\Sharp\Filters\DateRange\DateRangePreset;

/**
 * @internal
 */
trait DateRangeFilterTrait
{
    private string $dateFormat = 'YYYY-MM-DD';
    private bool $isMondayFirst = true;

    /** @var DateRangePreset[] */
    private ?array $presets = null;

    final public function configureDateFormat(string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;

        return $this;
    }

    final public function configureMondayFirst(bool $isMondayFirst = true): self
    {
        $this->isMondayFirst = $isMondayFirst;

        return $this;
    }

    final public function configureShowPresets(bool $showPresets = true, ?array $presets = null): self
    {
        if ($showPresets) {
            $this->presets = $presets !== null
                ? $presets
                : [
                    DateRangePreset::today(),
                    DateRangePreset::yesterday(),
                    DateRangePreset::last7days(),
                    DateRangePreset::last30days(),
                    DateRangePreset::last365days(),
                    DateRangePreset::thisMonth(),
                    DateRangePreset::lastMonth(),
                    DateRangePreset::thisYear(),
                    DateRangePreset::lastYear(),
                ];
        } else {
            $this->presets = [];
        }

        return $this;
    }

    final public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    final public function isMondayFirst(): bool
    {
        return $this->isMondayFirst;
    }

    /**
     * @internal
     */
    final public function fromQueryParam($value): ?array
    {
        if (! $value) {
            return null;
        }

        [$start, $end] = explode('..', $value);
        $start = Carbon::createFromFormat('Ymd', $start)->startOfDay();
        $end = Carbon::createFromFormat('Ymd', $end)->endOfDay();

        return [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'formatted' => [
                'start' => $start->isoFormat($this->getDateFormat()),
                'end' => $end->isoFormat($this->getDateFormat()),
            ],
        ];
    }

    /**
     * @internal
     */
    final public function toQueryParam($value): ?string
    {
        if (! $value) {
            return null;
        }

        return sprintf(
            '%s..%s',
            Carbon::parse($value['start'])->format('Ymd'),
            Carbon::parse($value['end'])->format('Ymd'),
        );
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'type' => FilterType::DateRange->value,
            'required' => $this instanceof DateRangeRequiredFilter,
            'mondayFirst' => $this->isMondayFirst(),
            'presets' => collect($this->presets)
                ->map(fn (DateRangePreset $preset) => $preset->toArray())
                ->toArray(),
        ]);
    }

    public function formatRawValue(mixed $value): DateRangeFilterValue
    {
        return new DateRangeFilterValue(Carbon::parse($value['start']), Carbon::parse($value['end']));
    }
}
