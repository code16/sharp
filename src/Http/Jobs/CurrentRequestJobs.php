<?php

namespace Code16\Sharp\Http\Jobs;

use Illuminate\Bus\PendingBatch;
use Illuminate\Support\Collection;

class CurrentRequestJobs extends PendingBatch
{
    public function afterSave(): PendingBatch
    {
    
    }
}
