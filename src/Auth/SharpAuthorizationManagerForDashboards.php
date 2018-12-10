<?php

namespace Code16\Sharp\Auth;

use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;
use Illuminate\Contracts\Auth\Access\Gate;

class SharpAuthorizationManagerForDashboards
{

    /**
     * @param string $ability
     * @param string $dashboardKey
     * @throws SharpAuthorizationException
     */
    public function checkForDashboard(string $ability, string $dashboardKey)
    {
        if(!$this->hasPolicyFor($dashboardKey)) {
            return;
        }

        if(!app(Gate::class)->check("sharp.{$dashboardKey}.{$ability}")) {
            $this->deny();
        }
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
        return config("sharp.dashboards.{$entityKey}.policy") != null;
    }
}