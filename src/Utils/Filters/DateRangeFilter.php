<?php

namespace Code16\Sharp\Utils\Filters;

use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonPeriod;

abstract class DateRangeFilter extends Filter
{
    private string $dateFormat = 'YYYY-MM-DD';
    private bool $isMondayFirst = true;

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

    final public function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    final public function isMondayFirst(): bool
    {
        return $this->isMondayFirst;
    }
    
    final public function getPresets(): array
    {
        return [
            'today' => CarbonPeriod::between('today', 'today'),
            'yesterday' => CarbonPeriod::between(today()->subDay(), 'today'),
            'last_7_days' => CarbonPeriod::between(today()->subDays(7), 'today'),
            'last_30_days' => CarbonPeriod::between(today()->subDays(30), 'today'),
            'last_365_days' => CarbonPeriod::between(today()->subDays(365), 'today'),
            'this_month' => CarbonPeriod::between(today()->startOfMonth(), today()->endOfMonth()),
            'last_month' => CarbonPeriod::between(today()->subMonth()->startOfMonth(), today()->subMonth()->endOfMonth()),
            'this_year' => CarbonPeriod::between(today()->startOfYear(), today()->endOfYear()),
            'last_year' => CarbonPeriod::between(today()->subYear()->startOfYear(), today()->subYear()->endOfYear()),
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
        
        return [
            'start' => Carbon::createFromFormat('Ymd', $start)->startOfDay(),
            'end' => Carbon::createFromFormat('Ymd', $end)->endOfDay(),
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
        
        return sprintf(
            '%s..%s',
            Carbon::parse($value['start'])->format('Ymd'),
            Carbon::parse($value['end'])->format('Ymd'),
        );
    }
}
