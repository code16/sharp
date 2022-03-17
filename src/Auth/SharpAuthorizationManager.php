<?php

namespace Code16\Sharp\Auth;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Contracts\Auth\Access\Gate;
use Illuminate\Support\Arr;

class SharpAuthorizationManager
{
    private array $cachedPolicies = [];

    public function __construct(protected SharpEntityManager $entityManager, protected Gate $gate)
    {
    }

    public function isAllowed(string $ability, string $entityKey, ?string $instanceId = null): bool
    {
        try {
            $this->check($ability, $entityKey, $instanceId);

            return true;
        } catch (SharpAuthorizationException) {
            return false;
        }
    }

    /**
     * @throws SharpAuthorizationException
     */
    public function check(string $ability, string $entityKey, ?string $instanceId = null): void
    {
        if ($this->isPolicyForbidden('entity', $entityKey)) {
            $this->deny();
        }

        // Check global authorization
        if ($this->isGloballyForbidden($ability, $entityKey)) {
            $this->deny();
        }

        if ($this->isPolicyForbidden($ability, $entityKey, $instanceId)) {
            $this->deny();
        }
    }

    protected function isGloballyForbidden(string $ability, string $entityKey): bool
    {
        return $this->entityManager
            ->entityFor($entityKey)
            ->isActionProhibited($ability);
    }

    protected function isPolicyForbidden(string $ability, string $entityKey, ?string $instanceId = null): bool
    {
        if (! Arr::exists($this->cachedPolicies, "$ability-$entityKey-$instanceId")) {
            $entity = $this->entityManager->entityFor($entityKey);
            $policy = $entity->getPolicyOrDefault();

            $forbidden = true;
            if (in_array($ability, ['entity', 'create'])) {
                // Always checked
                $forbidden = ! $policy->$ability(auth()->user());
            } elseif (in_array($ability, ['view', 'update', 'delete'])) {
                // Not checked in create case, as it could lead to unwanted errors in functional policy code (with findOrFail for instance)
                if ($instanceId || $entity->isSingle()) {
                    $forbidden = ! $policy->$ability(auth()->user(), $instanceId);
                }
            }

            $this->cachedPolicies["$ability-$entityKey-$instanceId"] = $forbidden;
        }

        return $this->cachedPolicies["$ability-$entityKey-$instanceId"];
    }

    private function deny()
    {
        throw new SharpAuthorizationException('Unauthorized action');
    }
}
