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
     * @param array $data
     * @return array
     */
    public function execute($instanceId, array $data = []): array
    {
        $spaceship = Spaceship::findOrFail($instanceId);

        return $this->view("sharp.spaceship-preview", compact('spaceship'));
    }
}