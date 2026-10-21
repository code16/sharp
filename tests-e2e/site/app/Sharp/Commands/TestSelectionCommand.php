<?php

namespace App\Sharp\Commands;

use App\Models\TestModel;
use Code16\Sharp\Commands\EntityCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestSelectionCommand extends EntityCommand
{
    public function label(): string
    {
        return 'Test selection command';
    }

    public function buildCommandConfig(): void
    {
        $this->configureInstanceSelectionAllowed();
    }

    public function execute(array $data = []): CommandReturn
    {
        TestModel::query()
            ->whereIn('id', $this->selectedIds())
            ->update(['text' => 'Selection changed']);

        return $this->reload();
    }

    public function authorize(): bool
    {
        return true;
    }
}
