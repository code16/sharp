<?php

namespace Code16\Sharp\Auth;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;

class SharpAuthorizationManager
{

    /**
     * @param string $ability
     * @param string $entityKey
     * @param string|null $instanceId
     * @throws SharpAuthorizationException
     */
    public function check(string $ability, string $entityKey, $instanceId = null)
    {
        $entityKey = $this->getBaseEntityKey($entityKey);

        if(config("sharp.entities.{$entityKey}")) {
            (new SharpAuthorizationManagerForEntities())
                ->checkForEntity($ability, $entityKey, $instanceId);

        } elseif(config("sharp.dashboards.{$entityKey}")) {
            (new SharpAuthorizationManagerForDashboards())
                ->checkForDashboard($ability, $entityKey);
        }
    }

    /**
     * Return base entityKey in case of sub entity (for instance: returns car in car:hybrid)
     *
     * @param string $entityKey
     * @return string
     */
    protected function getBaseEntityKey(string $entityKey): string
    {
        if(($pos = strpos($entityKey, ':')) !== false) {
            return substr($entityKey, 0, $pos);
        }

        return $entityKey;
    }
}