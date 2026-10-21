<?php

namespace Code16\Sharp\Filters;

/**
 * @internal
 */
trait AutocompleteRemoteSingleFilterTrait
{
    public function fromQueryParam($value): mixed
    {
        if (! $value) {
            return null;
        }

        return once(function () use ($value) {
            if ($label = $this->valueLabelFor($value)) {
                return [
                    'id' => $value,
                    'label' => $label,
                ];
            }

            return null;
        });
    }

    public function toQueryParam($value): mixed
    {
        return is_array($value) ? $value['id'] : $value;
    }

    public function formatRawValue(mixed $value): mixed
    {
        return $value ? $value['id'] : null;
    }

    abstract public function valueLabelFor(string $id): ?string;
}
