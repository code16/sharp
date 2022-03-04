<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\EntityListQueryParams;

class SpaceshipReload extends EntityCommand
{
    public function label(): string
    {
        return 'Reload full list';
    }

    public function execute(EntityListQueryParams $params, array $data = []): array
    {
        return $this->reload();
    }
}
