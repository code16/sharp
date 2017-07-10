<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;

class SpaceshipSynchronize extends EntityCommand
{

    /**
     * @return string
     */
    public function label(): string
    {
        return "Synchronize the gamma-spectrum";
    }
}