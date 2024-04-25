<?php

namespace Code16\Sharp\Utils\Filters;

use Carbon\Carbon;

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
    
    public function fromQueryParam($value): ?array
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
    
    public function toQueryParam($value): ?string
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
