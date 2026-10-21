<?php

namespace App\Sharp\Commands;

use Code16\Sharp\Commands\InstanceCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestLinkInstanceCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Test link command';
    }

    public function execute(mixed $instanceId, array $data = []): CommandReturn
    {
        return $this->link(route('test-page'));
    }
}
