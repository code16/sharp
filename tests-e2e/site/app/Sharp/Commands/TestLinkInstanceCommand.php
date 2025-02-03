<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;

class TestLinkInstanceCommand extends InstanceCommand
{
    public function label(): ?string
    {
        return 'Test link command';
    }

    public function execute(mixed $instanceId, array $data = []): array
    {
        return $this->link('https://example.org');
    }
}
