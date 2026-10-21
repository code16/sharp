<?php

namespace App\Sharp\Commands;

use App\Models\TestModel;
use Code16\Sharp\Commands\EntityCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestReloadEntityCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Test reload command';
    }

    public function execute(array $data = []): CommandReturn
    {
        TestModel::query()->update(['text' => 'Reloaded']);

        return $this->reload();
    }
}
