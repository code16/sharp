<?php

namespace Code16\Sharp\Filters;

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

        $value = collect($this->formattedValues())
            ->where('id', request('value'))
            ->first();

        session()->put($this->getSessionKey(), $value ? $value['id'] : null);
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
