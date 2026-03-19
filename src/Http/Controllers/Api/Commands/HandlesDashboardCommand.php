<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\Dashboard\Commands\DashboardWizardCommand;
use Code16\Sharp\Dashboard\SharpDashboard;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;

trait HandlesDashboardCommand
{
    protected function getDashboardCommandHandler(SharpDashboard $dashboard, string $commandKey)
    {
        $commandHandler = $dashboard->findDashboardCommandHandler($commandKey);
        $commandHandler->buildCommandConfig();

        $authorized = $commandHandler instanceof DashboardWizardCommand && ($step = $commandHandler->extractStepFromRequest())
            ? $commandHandler->authorizeForStep($step)
            : $commandHandler->authorize();

        if (! $authorized) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}
