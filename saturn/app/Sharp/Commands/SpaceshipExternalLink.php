<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;
use Code16\Sharp\Utils\Links\LinkToForm;

class SpaceshipExternalLink extends InstanceCommand
{
    public function label(): string
    {
        return "A Link to the Form";
    }

    public function execute($instanceId, array $data = []): array
    {
        return $this->link(
            LinkToForm::make('spaceship', $instanceId)->renderAsUrl()
        );
    }
}
