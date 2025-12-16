<?php

namespace Code16\Sharp\Filters;

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

    public function formatRawValue(mixed $value): mixed
    {
        return $value;
    }

    public function buildFilterConfig(): void {}

    protected function buildArray(array $childArray): array
    {
        return [
            'key' => $this->getKey(),
            'label' => $this->getLabel(),
            ...$childArray,
        ];
    }

    abstract public function toArray(): array;
    abstract public function fromQueryParam($value): mixed;
    abstract public function toQueryParam($value): mixed;
    abstract public function defaultValue(): mixed;
}
