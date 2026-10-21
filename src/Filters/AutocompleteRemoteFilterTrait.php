<?php

namespace Code16\Sharp\Filters;

use Code16\Sharp\Enums\FilterType;

/**
 * @internal
 */
trait AutocompleteRemoteFilterTrait
{
    private bool $isMaster = false;
    private int $debounceDelay = 300;
    private int $searchMinChars = 1;

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

    /**
     * Return the options matching the typed search, as [id => label].
     *
     * @return array<int|string, string>
     */
    abstract public function values(string $query): array;
}
