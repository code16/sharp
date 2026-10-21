<?php

namespace App\Sharp\Commands;

use Code16\Sharp\Commands\EntityCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestViewEntityCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Test view command';
    }

    public function execute(array $data = []): CommandReturn
    {
        return $this->view('command-view', ['title' => 'Command view']);
    }
}
