<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;

class TestDownloadInstanceCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Test download command';
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        return $this->download('file.pdf', 'file.pdf', 'fixtures');
    }
}
