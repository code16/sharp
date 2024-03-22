<?php

namespace Code16\Sharp\Http\Context\Concerns;


use Code16\Sharp\Http\Jobs\DispatchableAfterUpdate;

trait HandlesFormJobs
{
    protected array $afterFormUpdateJobs = [];
    
    /**
     * @return array<DispatchableAfterUpdate>
     */
    public function afterFormUpdateJobs(): array
    {
        return $this->afterFormUpdateJobs;
    }
    
    public function queueAfterFormUpdate($job)
    {
        $this->afterFormUpdateJobs[] = $job;
    }
}
