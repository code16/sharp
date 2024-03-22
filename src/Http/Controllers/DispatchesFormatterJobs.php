<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Http\Jobs\CurrentRequestJobs;

trait DispatchesFormatterJobs
{
    protected function dispatchFormatterJobs(?string $instanceId = null)
    {
        foreach (app(CurrentRequestJobs::class)->jobs as $job) {
            $job->setInstanceId($instanceId);
        }
        
        app(CurrentRequestJobs::class)
            ->allowFailures()
            ->dispatch();
    }
}
