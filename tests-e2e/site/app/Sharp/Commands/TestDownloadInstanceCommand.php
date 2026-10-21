<?php

namespace App\Sharp\Commands;

use Code16\Sharp\Commands\InstanceCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestDownloadInstanceCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Test download command';
    }

    public function execute(mixed $instanceId, array $data = []): CommandReturn
    {
        return $this->download('file.pdf', 'file.pdf', 'fixtures');
    }
}
