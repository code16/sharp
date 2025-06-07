<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\Exceptions\SharpException;

abstract class BaseSharpEntity
{
    protected bool $isDashboard = false;
    public static string $entityKey;
    protected ?string $policy = null;
    protected string $label = 'entity';
    protected ?string $icon = null;

    final public function getPolicyOrDefault(): SharpEntityPolicy
    {
        if (! $policy = $this->getPolicy()) {
            return new SharpEntityPolicy();
        }

        if (! $policy instanceof SharpEntityPolicy) {
            throw new SharpException('Policy class must implement '.SharpEntityPolicy::class);
        }

        return $policy;
    }

    abstract protected function getLabel(): string;

    final public function isDashboard(): bool
    {
        return $this->isDashboard;
    }

    final public function getIcon(): ?string
    {
        return $this->icon;
    }

    protected function getPolicy(): ?SharpEntityPolicy
    {
        return $this->policy ? app($this->policy) : null;
    }

    abstract public function isActionProhibited(string $action): bool;
}
