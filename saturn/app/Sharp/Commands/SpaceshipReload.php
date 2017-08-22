<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;

class SpaceshipReload extends EntityCommand
{

    /**
     * @return string
     */
    public function label(): string
    {
        return "Reload full list";
    }

    /**
     * @param array $params
     * @return array
     */
    public function execute(array $params = []): array
    {
        return $this->reload();
    }
}