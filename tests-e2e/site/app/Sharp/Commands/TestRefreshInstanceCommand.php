<?php

namespace App\Sharp\Commands;

use App\Models\TestModel;
use Code16\Sharp\Commands\InstanceCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestRefreshInstanceCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Test refresh command';
    }

    public function execute(mixed $instanceId, array $data = []): CommandReturn
    {
        TestModel::query()->update(['text' => 'Refreshed']);

        return $this->refresh(TestModel::query()->pluck('id')->all());
    }
}
