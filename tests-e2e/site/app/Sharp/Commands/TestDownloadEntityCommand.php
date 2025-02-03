<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;

class TestDownloadEntityCommand extends EntityCommand
{
    public function label(): ?string
    {
        return 'Test download command';
    }

    public function execute(array $data = []): array
    {
        return $this->download('file.pdf', 'file.pdf', 'fixtures');
    }
}
