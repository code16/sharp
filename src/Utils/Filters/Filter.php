<?php

namespace Code16\Sharp\Utils\Filters;

abstract class Filter
{
    protected ?string $label = null;
    protected bool $retainInSession = false;
    
    public function __construct()
    {
        $this->key = $this::class;
    }

    public function getKey(): string
    {
        return $this::class;
    }
    
    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function isRetainInSession(): bool
    {
        return $this->retainInSession;
    }

    public function configureLabel(string $label): self
    {
        $this->label = $label;
        return $this;
    }

    public function configureRetainInSession(bool $retainInSession = true): self
    {
        $this->retainInSession = $retainInSession;
        return $this;
    }

    public function buildFilterConfig(): void
    {
    }
}