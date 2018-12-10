<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;

class SpaceshipExternalLink extends InstanceCommand
{
    /**
     * @return string
     */
    public function label(): string
    {
        return "A Link";
    }

    /**
     * @param string $instanceId
     * @param array $data
     * @return array
     */
    public function execute($instanceId, array $data = []): array
    {
        return $this->link(
            route('code16.sharp.edit',
                [
                    'entityKey' => 'spaceship',
                    'instanceId' => $instanceId
                ]
            )
        );
    }
}
