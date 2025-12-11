<?php

namespace Code16\Sharp\Filters;

use Code16\Sharp\Exceptions\SharpInvalidFilterValueException;

abstract class GlobalRequiredFilter extends SelectRequiredFilter
{
    private mixed $currentValue = null;

    final public function currentValue(): mixed
    {
        return $this->currentValue !== null
            ? $this->currentValue
            : $this->defaultValue();
    }

    final public function setCurrentValue(mixed $value, bool $throwIfInvalid = true): void
    {
        if ($value === null) {
            $this->currentValue = null;

            return;
        }

        $formattedValue = collect($this->formattedValues())
            ->where('id', $value)
            ->first();

        if ($throwIfInvalid && ! $formattedValue) {
            throw new SharpInvalidFilterValueException('['.$value.'] is not a valid value for this filter.');
        }

        $this->currentValue = $formattedValue['id'] ?? null;
    }

    public function authorize(): bool
    {
        return true;
    }
}
