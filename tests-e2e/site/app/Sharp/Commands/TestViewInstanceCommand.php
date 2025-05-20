<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;

class TestViewInstanceCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Test view command';
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        return $this->view('command-view', ['title' => 'Command view']);
    }
}
