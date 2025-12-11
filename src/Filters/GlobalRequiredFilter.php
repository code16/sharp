<?php

namespace Code16\Sharp\Filters;

use Code16\Sharp\Exceptions\SharpInvalidFilterValueException;

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

        $formattedValue = collect($this->formattedValues())
            ->where('id', $value)
            ->first();

        if (! $formattedValue) {
            throw new SharpInvalidFilterValueException('['.$value.'] is not a valid value for this filter.');
        }

        session()->put($this->getSessionKey(), $formattedValue['id']);
    }

    private function getSessionKey(): string
    {
        return '_sharp_retained_global_filter_'.$this->getKey();
    }

    public function authorize(): bool
    {
        return true;
    }
}
