<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;

class SpaceshipReload extends EntityCommand
{
    public function label(): string
    {
        return 'Reload full list';
    }

    public function execute(array $data = []): array
    {
        return $this->reload();
    }
}
