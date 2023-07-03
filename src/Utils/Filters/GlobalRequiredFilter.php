<?php

namespace Code16\Sharp\Utils\Filters;

abstract class GlobalRequiredFilter extends SelectRequiredFilter
{
    final public function currentValue(): mixed
    {
        $value = session()->get($this->getSessionKey());

        if ($value === null) {
            if (($value = $this->defaultValue()) !== null) {
                session()->put($this->getSessionKey(), $value);
            }
        }

        return $value;
    }

    final public function setCurrentValue(mixed $value): void
    {
        if ($value === null) {
            session()->forget($this->getSessionKey());

            return;
        }

        session()->put($this->getSessionKey(), $value);
    }

    private function getSessionKey(): string
    {
        return '_sharp_retained_global_filter_'.$this->getKey();
    }
}
