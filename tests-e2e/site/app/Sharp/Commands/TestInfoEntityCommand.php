<?php

namespace App\Sharp\Commands;

use Code16\Sharp\Commands\EntityCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestInfoEntityCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Test info command';
    }

    public function execute(array $data = []): CommandReturn
    {
        return $this->info('Info message');
    }
}
