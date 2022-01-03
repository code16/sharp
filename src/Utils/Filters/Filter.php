<?php

namespace Code16\Sharp\Utils\Filters;

abstract class Filter
{
    protected ?string $customKey = null;
    protected ?string $label = null;
    protected bool $retainInSession = false;

    public function getKey(): string
    {
        return $this->customKey ?: class_basename($this::class);
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function isRetainInSession(): bool
    {
        return $this->retainInSession;
    }

    public function configureKey(string $key): self
    {
        $this->customKey = $key;
        return $this;
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