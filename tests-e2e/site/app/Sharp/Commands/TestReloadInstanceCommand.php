<?php

namespace App\Sharp\Commands;

use App\Models\TestModel;
use Code16\Sharp\EntityList\Commands\InstanceCommand;

class TestReloadInstanceCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Test reload command';
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        TestModel::query()->update(['text' => 'Reloaded']);

        return $this->reload();
    }
}
