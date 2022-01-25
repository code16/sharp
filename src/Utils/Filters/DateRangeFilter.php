<?php

namespace Code16\Sharp\Utils\Filters;

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
}
