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
     * @param array $params
     * @return array
     */
    public abstract function execute($instanceId, array $params = []);

    /**
     * Check if the current user is allowed to use this Command for this instance
     *
     * @return bool
     */
    public function authorizeFor($instanceId): bool
    {
        return true;
    }
}