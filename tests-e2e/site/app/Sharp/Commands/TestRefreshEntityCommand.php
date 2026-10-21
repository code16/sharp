<?php

namespace App\Sharp\Commands;

use App\Models\TestModel;
use Code16\Sharp\Commands\EntityCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestRefreshEntityCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Test refresh command';
    }

    public function execute(array $data = []): CommandReturn
    {
        TestModel::query()->update(['text' => 'Refreshed']);

        return $this->refresh(TestModel::query()->pluck('id')->all());
    }
}
