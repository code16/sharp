<?php

namespace App\Sharp\Commands;

use Code16\Sharp\Commands\InstanceCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestViewInstanceCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Test view command';
    }

    public function execute(mixed $instanceId, array $data = []): CommandReturn
    {
        return $this->view('command-view', ['title' => 'Command view']);
    }
}
