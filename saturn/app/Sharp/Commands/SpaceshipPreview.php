<?php

namespace App\Sharp\Commands;

use App\Spaceship;
use Code16\Sharp\EntityList\Commands\InstanceCommand;

class SpaceshipPreview extends InstanceCommand
{

    /**
     * @return string
     */
    public function label(): string
    {
        return "Preview public page";
    }

    /**
     * @param string $instanceId
     * @param array $params
     * @return array
     */
    public function execute($instanceId, array $params = [])
    {
        $spaceship = Spaceship::findOrFail($instanceId);

        return $this->view("sharp.spaceship-preview", compact('spaceship'));
    }
}