<?php

namespace Code16\Sharp\Utils\Filters;

abstract class SelectFilter extends Filter
{
    private bool $isMaster = false;
    private bool $isSearchable = false;
    private array $searchKeys = ["label"];
    private string $template = '{{label}}';

    public final function isMaster(): bool
    {
        return $this->isMaster;
    }

    public final function isSearchable(): bool
    {
        return $this->isSearchable;
    }

    public final function getSearchKeys(): array
    {
        return $this->searchKeys;
    }

    public final function getTemplate(): string
    {
        return $this->template;
    }
    
    public final function configureSearchable(bool $isSearchable = true): self
    {
        $this->isSearchable = $isSearchable;
        return $this;
    }

    public final function configureSearchKeys(array $searchKeys): self
    {
        $this->searchKeys = $searchKeys;
        return $this;
    }

    public final function configureTemplate(string $template): self
    {
        $this->template = $template;
        return $this;
    }

    public abstract function values(): array;
}

