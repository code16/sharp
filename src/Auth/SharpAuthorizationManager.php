<?php

namespace Code16\Sharp\Auth;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Illuminate\Contracts\Auth\Access\Gate;

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

        // Check entity-level policy authorization
        $this->checkEntityLevelAuthorization($entityKey);

        // Check global authorization
        if ($this->isGloballyForbidden($ability, $entityKey, $instanceId)) {
            $this->deny();
        }

        // Check policy authorization
        if($this->isSpecificallyForbidden($ability, $entityKey, $instanceId)) {
            $this->deny();
        }
    }

    /**
     * @param string $entityKey
     * @throws SharpAuthorizationException
     */
    protected function checkEntityLevelAuthorization(string $entityKey)
    {
        if($this->isSpecificallyForbidden("entity", $entityKey)) {
            $this->deny();
        }
    }

    /**
     * @param string $ability
     * @param string $entityKey
     * @param $instanceId
     * @return bool
     */
    protected function isGloballyForbidden(string $ability, string $entityKey, $instanceId): bool
    {
        $globalAuthorizations = config("sharp.entities.{$entityKey}.authorizations", []);

        if(!isset($globalAuthorizations[$ability])) {
            return false;
        }

        if(($instanceId && $ability == "view") || $ability == "create") {
            // Create or edit form case: we check for the global ability even on a GET
            return !$globalAuthorizations[$ability];
        }

        return request()->method() != 'GET'
            && !$globalAuthorizations[$ability];
    }

    /**
     * @param string $ability
     * @param string $entityKey
     * @param null $instanceId
     * @return bool
     */
    protected function isSpecificallyForbidden(string $ability, string $entityKey, $instanceId = null): bool
    {
        if(!$this->hasPolicyFor($entityKey)) {
            return false;
        }

        if($instanceId) {
            // Form case: edit, update, store, delete
            return !app(Gate::class)->check("sharp.{$entityKey}.{$ability}", $instanceId);
        }

        if(in_array($ability, ["entity", "create"])) {
            return !app(Gate::class)->check("sharp.{$entityKey}.{$ability}");
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

    /**
     * @throws SharpAuthorizationException
     */
    private function deny()
    {
        throw new SharpAuthorizationException("Unauthorized action");
    }

    /**
     * @param string $entityKey
     * @return bool
     */
    private function hasPolicyFor(string $entityKey)
    {
        return config("sharp.entities.{$entityKey}.policy") != null;
    }
}