<?php

namespace App\Sharp\Commands;

use App\Spaceship;
use Code16\Sharp\EntityList\Commands\InstanceCommand;

class SpaceshipPreview extends InstanceCommand
{

    public function label(): string
    {
        return "Preview public page";
    }

    public function execute($instanceId, array $data = []): array
    {
        $spaceship = Spaceship::findOrFail($instanceId);

        return $this->view("sharp.spaceship-preview", compact('spaceship'));
    }
}