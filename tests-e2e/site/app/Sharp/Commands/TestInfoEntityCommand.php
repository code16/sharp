<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;

class TestInfoEntityCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Test info command';
    }

    public function execute(array $data = []): array
    {
        return $this->info('Info message');
    }
}
