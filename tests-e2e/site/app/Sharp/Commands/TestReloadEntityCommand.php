<?php

namespace App\Sharp\Commands;

use App\Models\TestModel;
use Code16\Sharp\EntityList\Commands\EntityCommand;

class TestReloadEntityCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Test reload command';
    }

    public function execute(array $data = []): array
    {
        TestModel::query()->update(['text' => 'Reloaded']);

        return $this->reload();
    }
}
