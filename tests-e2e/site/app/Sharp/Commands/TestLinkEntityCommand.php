<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;

class TestLinkEntityCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Test link command';
    }

    public function execute(array $data = []): array
    {
        return $this->link(route('test-page'));
    }
}
