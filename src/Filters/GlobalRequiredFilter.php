<?php

namespace Code16\Sharp\Filters;

abstract class GlobalRequiredFilter extends SelectRequiredFilter
{
    private mixed $currentValue = null;

    final public function currentValue(): mixed
    {
        return $this->currentValue !== null
            ? $this->currentValue
            : $this->defaultValue();
    }

    final public function setCurrentValue(mixed $value): void
    {
        if ($value === null) {
            $this->currentValue = null;

            return;
        }

        $formattedValue = collect($this->formattedValues())
            ->where('id', $value)
            ->first();

        $this->currentValue = $formattedValue['id'] ?? null;
    }

    public function authorize(): bool
    {
        return true;
    }
}
