<?php

namespace Code16\Sharp\Utils\Entities;

use Code16\Sharp\Auth\SharpEntityPolicy;
use Code16\Sharp\Auth\SharpEntityPolicyLegacyDecorator;

abstract class BaseSharpEntity
{
    private bool $isDashboard = false;
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
        
        if(is_string($policy)) {
            $policy = app($policy);
            if(!$policy instanceof SharpEntityPolicy) {
                // Legacy (Sharp 6) policy
                return new SharpEntityPolicyLegacyDecorator($policy, $this->isDashboard);
            }
        } 
        
        return $policy;
    }

    protected function getPolicy(): string|SharpEntityPolicy|null
    {
        return $this->policy;
    }

    public abstract function isActionProhibited(string $action): bool;
}