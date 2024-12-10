<?php

namespace Code16\Sharp\Utils\Filters;

use Carbon\Carbon;

abstract class DateRangeFilter extends Filter
{
    private string $dateFormat = 'YYYY-MM-DD';
    private bool $isMondayFirst = true;
    private bool $showPresets = false;

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

    final public function configureShowPresets(bool $showPresets = true): self
    {
        $this->showPresets = $showPresets;

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
     * @return array<string, DateRangePreset>
     */
    final public function getPresets(): array
    {
        if (! $this->showPresets) {
            return [];
        }

        return [
            'today' => new DateRangePreset(
                start: today(),
                end: today(),
                label: __('sharp::filters.daterange.preset.today')
            ),
            'yesterday' => new DateRangePreset(
                start: today()->subDay(),
                end: today()->subDay(),
                label: __('sharp::filters.daterange.preset.yesterday')
            ),
            'last_7_days' => new DateRangePreset(
                start: today()->subDays(7),
                end: today(),
                label: __('sharp::filters.daterange.preset.last_7_days')
            ),
            'last_30_days' => new DateRangePreset(
                start: today()->subDays(30),
                end: today(),
                label: __('sharp::filters.daterange.preset.last_30_days')
            ),
            'last_365_days' => new DateRangePreset(
                start: today()->subDays(365),
                end: today(),
                label: __('sharp::filters.daterange.preset.last_365_days')
            ),
            'this_month' => new DateRangePreset(
                start: today()->startOfMonth(),
                end: today()->endOfMonth(),
                label: __('sharp::filters.daterange.preset.this_month')
            ),
            'last_month' => new DateRangePreset(
                start: today()->subMonth()->startOfMonth(),
                end: today()->subMonth()->endOfMonth(),
                label: __('sharp::filters.daterange.preset.last_month')
            ),
            'this_year' => new DateRangePreset(
                start: today()->startOfYear(),
                end: today()->endOfYear(),
                label: __('sharp::filters.daterange.preset.this_year')
            ),
            'last_year' => new DateRangePreset(
                start: today()->subYear()->startOfYear(),
                end: today()->subYear()->endOfYear(),
                label: __('sharp::filters.daterange.preset.last_year')
            ),
        ];
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
        $presetKey = collect($this->getPresets())
            ->search(fn (DateRangePreset $preset) => $preset->getStart()->isSameDay($start)
                && $preset->getEnd()->isSameDay($end));

        return [
            'start' => $start->format('Y-m-d'),
            'end' => $end->format('Y-m-d'),
            'formatted' => [
                'start' => $start->isoFormat($this->getDateFormat()),
                'end' => $end->isoFormat($this->getDateFormat()),
            ],
            'preset' => $presetKey ?: null,
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

        if (isset($value['preset'])) {
            if ($preset = $this->getPresets()[$value['preset']] ?? null) {
                return sprintf(
                    '%s..%s',
                    $preset->getStart()->format('Ymd'),
                    $preset->getEnd()->format('Ymd'),
                );
            }

            return null;
        }

        return sprintf(
            '%s..%s',
            Carbon::parse($value['start'])->format('Ymd'),
            Carbon::parse($value['end'])->format('Ymd'),
        );
    }

    public function formatRawValue(mixed $value): DateRangeFilterValue
    {
        return new DateRangeFilterValue(Carbon::parse($value['start']), Carbon::parse($value['end']));
    }
}
