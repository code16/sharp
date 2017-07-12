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

    /**
     * @param array $params
     * @return array
     */
    public function execute(array $params = [])
    {
        return $this->info("Gamma spectrum synchronized!");
    }

    public function confirmationText()
    {
        return "Sure, really?";
    }

    public function authorize():bool
    {
        return auth("sharp")->user()->hasGroup("boss");
    }
}