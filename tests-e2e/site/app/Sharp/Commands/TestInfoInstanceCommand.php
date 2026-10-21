<?php

namespace App\Sharp\Commands;

use Code16\Sharp\Commands\InstanceCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestInfoInstanceCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Test info command';
    }

    public function execute(mixed $instanceId, array $data = []): CommandReturn
    {
        return $this->info('Info message');
    }
}
