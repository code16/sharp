<?php

namespace Code16\Sharp\Filters;

use Code16\Sharp\Enums\FilterType;

abstract class AutocompleteRemoteFilter extends Filter
{
    private bool $isMaster = false;
    private int $debounceDelay = 300;
    private int $searchMinChars = 1;
    private array $cache = [];

    final public function configureMaster(bool $isMaster = true): self
    {
        $this->isMaster = $isMaster;

        return $this;
    }

    final public function configureDebounceDelay(int $delay): self
    {
        $this->debounceDelay = $delay;

        return $this;
    }

    final public function configureSearchMinChars(int $minChars): self
    {
        $this->searchMinChars = $minChars;

        return $this;
    }

    public function fromQueryParam($value): mixed
    {
        if ($value) {
            $value = collect(explode(',', $value));

            return $this->cache[$value->sort()->implode(',')] ??= $this->format($this->valuesFor($value->all()));
        }

        return [];
    }

    public function toQueryParam($value): mixed
    {
        return $value
            ? collect($value)->pluck('id')->join(',')
            : null;
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'type' => FilterType::AutocompleteRemote->value,
            'master' => $this->isMaster,
            'required' => $this instanceof AutocompleteRemoteRequiredFilter,
            'multiple' => $this instanceof AutocompleteRemoteMultipleFilter,
            'debounceDelay' => $this->debounceDelay,
            'searchMinChars' => $this->searchMinChars,
        ]);
    }

    final public function format(array $values)
    {
        if (! is_array(collect($values)->first())) {
            return collect($values)
                ->map(function ($label, $id) {
                    return compact('id', 'label');
                })
                ->values()
                ->all();
        }

        return $values;
    }

    public function formatRawValue(mixed $value): mixed
    {
        return $value ? $value[0]['id'] : null;
    }

    abstract public function values(string $query): array;

    abstract public function valuesFor(array $ids): array;
}
