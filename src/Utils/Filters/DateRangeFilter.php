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
        if(!$this->showPresets) {
            return [];
        }
        
        return [
            'today' => new DateRangePreset(
                Carbon::today(), Carbon::today(),
                'Today'
            ),
            'yesterday' => new DateRangePreset(Carbon::yesterday(), Carbon::yesterday(), 'Yesterday'),
            'last_7_days' => new DateRangePreset(Carbon::today()->subDays(7), Carbon::today(), 'Last 7 days'),
            'last_30_days' => new DateRangePreset(Carbon::today()->subDays(30), Carbon::today(), 'Last 30 days'),
            'last_365_days' => new DateRangePreset(Carbon::today()->subDays(365), Carbon::today(), 'Last 365 days'),
            'this_month' => new DateRangePreset(Carbon::today()->startOfMonth(), Carbon::today()->endOfMonth(), 'This month'),
            'last_month' => new DateRangePreset(Carbon::today()->subMonth()->startOfMonth(), Carbon::today()->subMonth()->endOfMonth(), 'Last month'),
            'this_year' => new DateRangePreset(Carbon::today()->startOfYear(), Carbon::today()->endOfYear(), 'This year'),
            'last_year' => new DateRangePreset(Carbon::today()->subYear()->startOfYear(), Carbon::today()->subYear()->endOfYear(), 'Last year'),
        ];
    }
    
    /**
     * @internal
     */
    final public function fromQueryParam($value): ?array
    {
        if(!$value) {
            return null;
        }
        
        [$start, $end] = explode('..', $value);
        $start = Carbon::createFromFormat('Ymd', $start)->startOfDay();
        $end = Carbon::createFromFormat('Ymd', $end)->endOfDay();
        $presetKey = collect($this->getPresets())
            ->search(fn (DateRangePreset $preset) => $preset->getStart()->isSameDay($start) && $preset->getEnd()->isSameDay($end));
        
        return [
            'start' => $start,
            'end' => $end,
            'preset' => $presetKey ?: null,
        ];
    }
    
    /**
     * @internal
     */
    final public function toQueryParam($value): ?string
    {
        if(!$value) {
            return null;
        }
        
        if(isset($value['preset'])) {
            if($preset = $this->getPresets()[$value['preset']] ?? null) {
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
}
