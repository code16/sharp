<?php

namespace Code16\Sharp\Utils\Filters;

abstract class DateRangeFilter extends Filter
{
    private string $dateFormat = 'YYYY-MM-DD';
    private bool $isMondayFirst = true;

    public final function configureDateFormat(string $dateFormat): self
    {
        $this->dateFormat = $dateFormat;
        return $this;
    }

    public final function configureMondayFirst(bool $isMondayFirst = true): self
    {
        $this->isMondayFirst = $isMondayFirst;
        return $this;
    }

    public final function getDateFormat(): string
    {
        return $this->dateFormat;
    }

    public final function isMondayFirst(): bool
    {
        return $this->isMondayFirst;
    }
}

