<?php

namespace App\Sharp\Commands;

use Code16\Sharp\Commands\EntityCommand;
use Code16\Sharp\Commands\Returns\CommandReturn;

class TestDownloadEntityCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Test download command';
    }

    public function execute(array $data = []): CommandReturn
    {
        return $this->download('file.pdf', 'file.pdf', 'fixtures');
    }
}
