<?php

namespace App\Sharp\Commands;

use Code16\Sharp\Commands\EntityCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestLinkEntityCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Test link command';
    }

    public function execute(array $data = []): CommandReturn
    {
        return $this->link(route('test-page'));
    }
}
