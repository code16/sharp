<?php

namespace Code16\Sharp\Filters;

abstract class RemoteAutocompleteFilter extends Filter
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
            'type' => 'remoteAutocomplete',
            'master' => $this->isMaster(),
            'required' => $this instanceof RemoteAutocompleteRequiredFilter,
            'multiple' => $this instanceof RemoteAutocompleteMultipleFilter,
        ]);
    }

    abstract public function values(string $query): array;

    abstract public function valuesFor(array $ids): array;
}
