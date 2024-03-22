<?php

namespace Code16\Sharp\Http\Jobs;

trait AfterSaveDispatchable
{
    protected ?string $instanceId;
    
    public static function dispatchAfterSave(...$arguments): void
    {
        app(CurrentRequestJobs::class)->add(new static(...$arguments));
    }
    
    public function setInstanceId(?string $instanceId): static
    {
        $this->instanceId = $instanceId;
        
        return $this;
    }
}
