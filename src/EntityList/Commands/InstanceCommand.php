<?php

namespace Code16\Sharp\EntityList\Commands;

abstract class InstanceCommand extends Command
{

    /**
     * @return string
     */
    public function type(): string
    {
        return "instance";
    }

    /**
     * @param string $instanceId
     * @return array
     */
    public abstract function execute($instanceId);
}