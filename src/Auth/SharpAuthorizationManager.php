<?php

namespace Code16\Sharp\Auth;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Code16\Sharp\Utils\Entities\SharpEntityManager;
use Illuminate\Contracts\Auth\Access\Gate;

class SharpAuthorizationManager
{
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
        $policy = $this->entityManager->entityFor($entityKey)->getPolicyOrDefault();

        if (in_array($ability, ['entity', 'create'])) {
            return !$policy->$ability(auth()->user());
        }

        if (in_array($ability, ['view', 'update', 'delete'])) {
            return !$policy->$ability(auth()->user(), $instanceId);
        }

        return true;
    }

    private function deny()
    {
        throw new SharpAuthorizationException('Unauthorized action');
    }
}
