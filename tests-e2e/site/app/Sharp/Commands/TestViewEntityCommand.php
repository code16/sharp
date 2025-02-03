<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;

class TestViewEntityCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Test view command';
    }

    public function execute(array $data = []): array
    {
        return $this->view('command-view', ['title' => 'Command view']);
    }
}
