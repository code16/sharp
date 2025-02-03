<?php

namespace App\Sharp\Commands;

use App\Models\TestModel;
use Code16\Sharp\EntityList\Commands\InstanceCommand;

class TestRefreshInstanceCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Test refresh command';
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        TestModel::query()->update(['text' => 'Refreshed']);

        return $this->refresh(TestModel::query()->pluck('id')->all());
    }
}
