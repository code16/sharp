<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\EntityListQueryParams;

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
     * @param EntityListQueryParams $params
     * @param array $data
     * @return array
     */
    public function execute(EntityListQueryParams $params, array $data=[]): array
    {
        return $this->reload();
    }
}