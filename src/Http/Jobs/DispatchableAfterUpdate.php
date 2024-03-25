<?php

namespace Code16\Sharp\Http\Jobs;

trait DispatchableAfterUpdate
{
    public ?string $instanceId = null;
    
    public function setInstanceId(?string $instanceId): static
    {
        $this->instanceId = $instanceId;
        
        return $this;
    }
}