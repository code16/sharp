<?php

namespace Code16\Sharp\Http\Controllers\Api\Commands;

use Code16\Sharp\EntityList\Commands\EntityCommand;
use Code16\Sharp\EntityList\Commands\Wizards\EntityWizardCommand;
use Code16\Sharp\EntityList\SharpEntityList;
use Code16\Sharp\Exceptions\Auth\SharpAuthorizationException;

trait HandlesEntityCommand
{
    protected function getEntityCommandHandler(SharpEntityList $list, string $commandKey): ?EntityCommand
    {
        $commandHandler = $list->findEntityCommandHandler($commandKey);
        $commandHandler->buildCommandConfig();

        $authorized = $commandHandler instanceof EntityWizardCommand && ($step = $commandHandler->extractStepFromRequest())
            ? $commandHandler->authorizeForStep($step)
            : $commandHandler->authorize();

        if (! $authorized) {
            throw new SharpAuthorizationException();
        }

        return $commandHandler;
    }
}
