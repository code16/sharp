<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\Auth\SharpEntityPolicyLegacyDecorator;

abstract class BaseSharpEntity
{
    protected bool $isDashboard = false;
    protected string $entityKey = 'entity';
    protected ?string $policy = null;
    protected string $label = 'entity';

    final public function setEntityKey(string $entityKey): self
    {
        $this->entityKey = $entityKey;

        return $this;
    }

    final public function getPolicyOrDefault(): SharpEntityPolicy
    {
        if (! $policy = $this->getPolicy()) {
            return new SharpEntityPolicy();
        }

        if (is_string($policy)) {
            $policy = app($policy);
            if (! $policy instanceof SharpEntityPolicy) {
                // Legacy (Sharp 6) policy
                return new SharpEntityPolicyLegacyDecorator($policy, $this->isDashboard);
            }
        }

        return $policy;
    }

    final public function getLabel(): string
    {
        return $this->label;
    }

    final public function isDashboard(): bool
    {
        return $this->isDashboard;
    }

    protected function getPolicy(): string|SharpEntityPolicy|null
    {
        return $this->policy;
    }

    abstract public function isActionProhibited(string $action): bool;
}
