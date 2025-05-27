<?php

namespace Code16\Sharp\Filters;

use Code16\Sharp\Enums\FilterType;

abstract class AutocompleteRemoteFilter extends Filter
{
    private bool $isMaster = false;

    final public function isMaster(): bool
    {
        return $this->isMaster;
    }

    final public function configureMaster(bool $isMaster = true): self
    {
        $this->isMaster = $isMaster;

        return $this;
    }

    public function fromQueryParam($value): mixed
    {
        return $value ? $this->valuesFor([$value]) : null;
    }

    public function toQueryParam($value): mixed
    {
        return $value;
    }

    public function toArray(): array
    {
        return parent::buildArray([
            'type' => FilterType::AutocompleteRemote->value,
            'master' => $this->isMaster(),
            'required' => $this instanceof AutocompleteRemoteRequiredFilter,
            'multiple' => $this instanceof AutocompleteRemoteMultipleFilter,
        ]);
    }

    abstract public function values(string $query): array;

    abstract public function valuesFor(array $ids): array;
}
