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
     */
    public function check(string $ability, string $entityKey, $instanceId = null)
    {
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

    protected function checkEntityLevelAuthorization(string $entityKey)
    {
        if($this->isSpecificallyForbidden("entity", $entityKey)) {
            $this->deny();
        }
    }

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

    protected function isSpecificallyForbidden(string $ability, string $entityKey, $instanceId = null): bool
    {
        if(!$this->hasPolicyFor($entityKey)) {
            return false;
        }

        if($instanceId) {
            // Form case: edit, update, store, delete
            return request()->method() != 'GET'
                && !app(Gate::class)->check("sharp.{$entityKey}.{$ability}", $instanceId);
        }

        if(in_array($ability, ["entity", "create"])) {
            return !app(Gate::class)->check("sharp.{$entityKey}.{$ability}");
        }
    }

    private function deny()
    {
        throw new SharpAuthorizationException("Unauthorized action");
    }

    private function hasPolicyFor(string $entityKey)
    {
        return config("sharp.entities.{$entityKey}.policy") != null;
    }
}