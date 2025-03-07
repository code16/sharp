<?php

namespace Code16\Sharp\Utils\Filters;

abstract class SelectFilter extends Filter
{
    private bool $isMaster = false;
    private bool $isSearchable = false;
    private array $searchKeys = ['label'];

    final public function isMaster(): bool
    {
        return $this->isMaster;
    }

    final public function isSearchable(): bool
    {
        return $this->isSearchable;
    }

    final public function getSearchKeys(): array
    {
        return $this->searchKeys;
    }

    final public function configureSearchable(bool $isSearchable = true): self
    {
        $this->isSearchable = $isSearchable;

        return $this;
    }

    final public function configureSearchKeys(array $searchKeys): self
    {
        $this->searchKeys = $searchKeys;

        return $this;
    }

    final public function configureMaster(bool $isMaster = true): self
    {
        $this->isMaster = $isMaster;

        return $this;
    }

    public function fromQueryParam($value): mixed
    {
        return $value;
    }

    public function toQueryParam($value): mixed
    {
        return $value;
    }

    abstract public function values(): array;
}
