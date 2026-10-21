<?php

namespace App\Sharp\Commands;

use App\Models\TestModel;
use Code16\Sharp\Commands\InstanceCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestReloadInstanceCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Test reload command';
    }

    public function execute(mixed $instanceId, array $data = []): CommandReturn
    {
        TestModel::query()->update(['text' => 'Reloaded']);

        return $this->reload();
    }
}
