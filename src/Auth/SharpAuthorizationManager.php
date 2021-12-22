<?php

namespace Code16\Sharp\Auth;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Contracts\Auth\Access\Gate;

class SharpAuthorizationManager
{
    public function __construct(protected SharpEntityManager $entityManager) {}

    public function check(string $ability, string $entityKey, ?string $instanceId = null): void
    {
        
        if($this->isForbidden("entity", $entityKey)) {
            $this->deny();
        }
        
        if($this->isForbidden($ability, $entityKey, $instanceId)) {
            $this->deny();
        }

//        // Check global authorization
//        if ($this->isGloballyForbidden($ability, $entityKey, $instanceId)) {
//            $this->deny();
//        }
    }

    protected function isForbidden(string $ability, string $entityKey, ?string $instanceId = null): bool
    {
        if($instanceId) {
            // Form case: view, update, create, delete
            return !app(Gate::class)->check("sharp.{$entityKey}.{$ability}", $instanceId);
        }

        if(in_array($ability, ["entity", "create"])) {
            return !app(Gate::class)->check("sharp.{$entityKey}.{$ability}");
        }

        return false;
    }

    private function deny()
    {
        throw new SharpAuthorizationException("Unauthorized action");
    }
}