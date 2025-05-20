<?php

namespace App\Sharp;

use App\Models\TestModel;
use Code16\Sharp\EntityList\Commands\EntityState;

class TestModelStateHandler extends EntityState
{
    protected function buildStates(): void
    {
        $this->addState('draft', 'Draft', 'orange')
            ->addState('published', 'Published', '#0c4589');
    }

    protected function updateState($instanceId, string $stateId): ?array
    {
        TestModel::find($instanceId)->update(['state' => $stateId]);

        return $this->refresh($instanceId);
    }
}
