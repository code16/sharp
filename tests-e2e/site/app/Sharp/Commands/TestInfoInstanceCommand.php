<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;

class TestInfoInstanceCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Test info command';
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        return $this->info('Info message');
    }
}
