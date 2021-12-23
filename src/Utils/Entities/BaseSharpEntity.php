<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Auth\SharpEntityPolicy;

abstract class BaseSharpEntity
{
    protected string $entityKey = "entity";
    protected ?string $policy = null;

    public final function setEntityKey(string $entityKey): self
    {
        $this->entityKey = $entityKey;
        return $this;
    }
    
    public final function getPolicyOrDefault(): SharpEntityPolicy
    {
        if(!$policy = $this->getPolicy()) {
            return new SharpEntityPolicy();
        }
        return is_string($policy) ? app($policy) : $policy;
    }

    protected function getPolicy(): string|SharpEntityPolicy|null
    {
        return $this->policy;
    }

    public abstract function isActionProhibited(string $action): bool;
}