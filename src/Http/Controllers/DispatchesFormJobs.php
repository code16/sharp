<?php

namespace Code16\Sharp\Http\Controllers;

use Code16\Sharp\Http\Context\CurrentSharpRequest;

trait DispatchesFormJobs
{
    public function dispatchAfterUpdateJobs(?string $instanceId = null)
    {
        foreach (app(CurrentSharpRequest::class)->afterFormUpdateJobs() as $job) {
            dispatch($job->setInstanceId($instanceId));
        }
    }
}
