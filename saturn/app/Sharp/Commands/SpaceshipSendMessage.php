<?php

namespace App\Sharp\Commands;

use Code16\Sharp\EntityList\Commands\InstanceCommand;

class SpaceshipSendMessage extends InstanceCommand
{

    /**
     * @return string
     */
    public function label(): string
    {
        return "Send a text message";
    }

    /**
     * @param string $instanceId
     * @return array
     */
    public function execute($instanceId)
    {
        // TODO execute method

        return $this->refresh($instanceId);
    }
}